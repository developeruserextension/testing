<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup\Operation;

use Amasty\ProductAttachment\Controller\Adminhtml\RegistryConstants;
use Amasty\ProductAttachment\Model\File\FileScope\SaveFileScopeInterface;
use Amasty\ProductAttachment\Setup\InstallData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\ProductAttachment\Model\Icon\IconFactory;
use Amasty\ProductAttachment\Model\Icon\Repository as IconRepository;
use Amasty\ProductAttachment\Model\File\FileFactory;
use Amasty\ProductAttachment\Model\File\Repository as FileRepository;
use Amasty\ProductAttachment\Model\SourceOptions\AttachmentType;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Amasty\Base\Helper\Deploy;

class UpgradeDataTo200
{
    /**
     * @var IconFactory
     */
    private $iconFactory;

    /**
     * @var IconRepository
     */
    private $iconRepository;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var Deploy
     */
    private $deploy;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var array
     */
    private $savedFiles = [];

    /**
     * @var array
     */
    private $fileIds = [];

    /**
     * @var SaveFileScopeInterface
     */
    private $saveFileScope;

    public function __construct(
        IconFactory $iconFactory,
        IconRepository $iconRepository,
        FileFactory $fileFactory,
        FileRepository $fileRepository,
        FileSystem $filesystem,
        Deploy $deploy,
        ComponentRegistrarInterface $componentRegistrar,
        SaveFileScopeInterface $saveFileScope
    ) {
        $this->iconFactory = $iconFactory;
        $this->iconRepository = $iconRepository;
        $this->fileFactory = $fileFactory;
        $this->fileRepository = $fileRepository;
        $this->fileSystem = $filesystem;
        $this->deploy = $deploy;
        $this->componentRegistrar = $componentRegistrar;
        $this->saveFileScope = $saveFileScope;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->moveIcons($setup);
        $this->moveFiles($setup);
        $this->moveReport($setup);
        $this->moveSettings($setup);
        $this->removeHtaccess();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function moveIcons(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $iconsSelect = $connection->select()->from($setup->getTable(RenameOldTables::AMASTY_FILE_ICON_OLD));
        $icons = $connection->fetchAll($iconsSelect);
        $oldIcons = [];

        $this->deploy->deployFolder(
            $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                'Amasty_ProductAttachment'
            ) . DIRECTORY_SEPARATOR . InstallData::DEPLOY_DIR
        );

        foreach ($icons as $icon) {
            $oldIcons[$icon['type']] = [
                'filename' => $icon['image'],
                'extensions' => [$icon['type']]
            ];
        }
        $icons = array_merge(InstallData::FILE_TYPE_ICONS, $oldIcons);
        foreach ($icons as $type => $iconData) {
            /** @var \Amasty\ProductAttachment\Model\Icon\Icon $icon */
            $icon = $this->iconFactory->create();
            $icon->setFileType($type)
                ->setImage($iconData['filename'])
                ->setIsActive(1)
                ->setExtension($iconData['extensions']);
            try {
                $this->iconRepository->save($icon);
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                //so sad:(
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function moveFiles(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $filesSelect = $connection
            ->select()
            ->from(
                ['main_table' => $setup->getTable(RenameOldTables::AMASTY_FILE_OLD)]
            )->joinLeft(
                ['groups' => $setup->getTable(RenameOldTables::AMASTY_FILE_CUSTOMER_GROUP_OLD)],
                'main_table.id = groups.file_id',
                ['customer_groups' => 'GROUP_CONCAT(customer_group_id)']
            )->joinLeft(
                ['store' => $setup->getTable(RenameOldTables::AMASTY_FILE_STORE_OLD)],
                'main_table.id = store.file_id',
                ['label', 'is_visible', 'show_for_ordered']
            )->group('main_table.id');

        $files = $connection->fetchAll($filesSelect);

        $mediaPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath()
            . 'amasty' . DIRECTORY_SEPARATOR
            . 'amfile' . DIRECTORY_SEPARATOR . 'attach' . DIRECTORY_SEPARATOR;

        foreach ($files as $oldFiles) {
            /** @var \Amasty\ProductAttachment\Model\File\File $file */
            $file = $this->fileFactory->create();
            if (!empty($oldFiles['product_id'])) {
                $file->setProducts([$oldFiles['product_id']]);
            }
            if (!empty($oldFiles['category_id'])) {
                $file->setCategories([$oldFiles['category_id']]);
            }
            if (isset($oldFiles['customer_groups'])) {
                $customerGroups = explode(',', $oldFiles['customer_groups']);
            } else {
                $customerGroups = [];
            }
            $file->setCustomerGroups($customerGroups);
            $path = explode('.', $oldFiles['file_path']);
            $extension = end($path);
            if ($oldFiles['file_type'] == 'url') {
                $file->setAttachmentType(AttachmentType::LINK);
            } else {
                $file->setAttachmentType(AttachmentType::FILE);
            }
            $file->setLink($oldFiles['file_url']);
            $file->setFileExtension($extension);
            $file->setFileName($oldFiles['file_name']);
            $fileInfo = [
                'name' => '',
                'tmp_name' => $mediaPath . $oldFiles['file_path'],
                'file' => $mediaPath . $oldFiles['file_path']
            ];
            $file->setFile([$fileInfo]);
            $file->setLabel($oldFiles['label']);
            $file->setIsVisible($oldFiles['is_visible']);
            $file->setIsIncludeInOrder($oldFiles['show_for_ordered']);
            try {
                if (!isset($this->savedFiles[$oldFiles['file_path']])) {
                    $file = $this->fileRepository->saveAll($file, [], false);
                    $this->savedFiles[$oldFiles['file_path']] = $file->getFileId();
                } else {
                    if ($products = $file->getProducts()) {
                        $file->setFileId($this->savedFiles[$oldFiles['file_path']]);
                        $file->setData('link', '');
                        $file->setData('file', '');
                        $file->setData('position', 0);
                        $this->saveFileScope->execute(
                            [
                                RegistryConstants::FILES => [
                                    $file,
                                ],
                                RegistryConstants::PRODUCT => $products[0]
                            ],
                            'product'
                        );
                    }

                    if ($categories = $file->getCategories()) {
                        $file->setFileId($this->savedFiles[$oldFiles['file_path']]);
                        $file->setData('link', '');
                        $file->setData('file', '');
                        $file->setData('position', 0);
                        $this->saveFileScope->execute(
                            [
                                RegistryConstants::FILES => [
                                    $file
                                ],
                                RegistryConstants::CATEGORY => $categories[0]
                            ],
                            'category'
                        );
                    }
                }

                $this->fileIds[$oldFiles['id']] = $file->getFileId();
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                // do nothing
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function moveReport(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $statSelect = $connection->select()->from(
            $setup->getTable(RenameOldTables::AMASTY_FILE_STAT_OLD),
            [
                'file_id' => 'file_id',
                'product_id' => 'product_id',
                'store_id' => 'store_id',
                'customer_id' => 'customer_id',
                'downloaded_at' => 'downloaded_at'
            ]
        );
        $results = $connection->fetchAll($statSelect);
        foreach ($results as $result) {
            if (isset($this->fileIds[$result['file_id']])) {
                $connection->insert(
                    $setup->getTable(CreateReportTable::TABLE_NAME),
                    [
                        'file_id' => $this->fileIds[$result['file_id']],
                        'product_id' => $result['product_id'],
                        'store_id' => $result['store_id'],
                        'customer_id' => $result['customer_id'],
                        'downloaded_at' => $result['downloaded_at'],
                        'category_id' => 0,
                        'order_id' => null,
                        'download_source' => 'product'
                    ]
                );
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function moveSettings(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $values = [
            'amfile/product_tab/sibling_tab' => 'amfile/block/sibling_tab',
            'amfile/product_tab/sibling_tab_custom' => 'amfile/block/sibling_tab_custom',
            'amfile/product_tab/customer_group' => 'amfile/block/customer_group',
            'amfile/import/ftp_dir' => 'amfile/block/import',
            'amfile/additional/detect_mime' => 'amfile/block/aditional',
            'amfile/product_tab/block_enabled' => 'amfile/block/display_block',
            'amfile/product_tab/position' => 'amfile/block/position'
        ];
        foreach ($values as $newValue => $oldValue) {
            $connection->update($setup->getTable('core_config_data'), ['path' => $newValue], ['path = ?' => $oldValue]);
        }
    }

    private function removeHtaccess()
    {
        $ds = DIRECTORY_SEPARATOR;
        try {
            $write = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
            $write->delete('amasty' . $ds . 'amfile' . $ds . 'attach' . $ds . '.htaccess');
            $write->delete('amasty' . $ds . 'amfile' . $ds . 'tmp' . $ds . 'attach' . $ds . '.htaccess');
        } catch (\Exception $e) {
            // do nothing;
        }
    }
}
