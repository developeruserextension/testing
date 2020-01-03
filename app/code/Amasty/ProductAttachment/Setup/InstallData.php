<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Component\ComponentRegistrar;

class InstallData implements InstallDataInterface
{
    const DEPLOY_DIR = 'pub';

    const FILE_TYPE_ICONS = [
        'Document' => [
            'filename' => 'Document.png',
            'extensions' => [
                'doc',
                'docx',
                'txt',
                'rtf',
                'pdf',
                'djvu'
            ]
        ],
        'Image' => [
            'filename' => 'Image.png',
            'extensions' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'bmp'
            ]
        ],
        'Video' => [
            'filename' => 'Video.png',
            'extensions' => [
                'avi',
                'mp4'
            ]
        ],
        'Audio' => [
            'filename' => 'Audio.png',
            'extensions' => [
                'mp3',
                'jpeg',
                'ogg'
            ]
        ],
        'Archive' => [
            'filename' => 'Archive.png',
            'extensions' => [
                'zip',
                'rar',
                '7z'
            ]
        ],
        'Table' => [
            'filename' => 'Table.png',
            'extensions' => [
                'csv',
                'xls',
                'xlsx'
            ]
        ],
        'Presentation' => [
            'filename' => 'Presentation.png',
            'extensions' => [
                'pptx',
                'pptm',
                'ppt'
            ]
        ],
        'Scheme' => [
            'filename' => 'Scheme.png',
            'extensions' => [
                'ini'
            ]
        ],
        'Service' => [
            'filename' => 'Service.png',
            'extensions' => [
                'ini'
            ]
        ],
    ];

    /**
     * @var \Amasty\Base\Helper\Deploy
     */
    private $deploy;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var \Amasty\ProductAttachment\Model\Icon\Repository
     */
    private $repository;

    /**
     * @var \Amasty\ProductAttachment\Model\Icon\IconFactory
     */
    private $iconFactory;

    public function __construct(
        \Amasty\Base\Helper\Deploy $deploy,
        \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar,
        \Amasty\ProductAttachment\Model\Icon\Repository $repository,
        \Amasty\ProductAttachment\Model\Icon\IconFactory $iconFactory
    ) {

        $this->deploy = $deploy;
        $this->componentRegistrar = $componentRegistrar;
        $this->repository = $repository;
        $this->iconFactory = $iconFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->deploy->deployFolder(
            $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                'Amasty_ProductAttachment'
            ) . DIRECTORY_SEPARATOR . self::DEPLOY_DIR
        );

        $setup->startSetup();

        foreach (self::FILE_TYPE_ICONS as $type => $iconData) {
            /** @var \Amasty\ProductAttachment\Model\Icon\Icon $icon */
            $icon = $this->iconFactory->create();
            $icon->setFileType($type)
                ->setImage($iconData['filename'])
                ->setIsActive(1)
                ->setExtension($iconData['extensions']);

            try {
                $this->repository->save($icon);
            } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                //so sad:(
            }
        }

        $setup->endSetup();
    }
}
