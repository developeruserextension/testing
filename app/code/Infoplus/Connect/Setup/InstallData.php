<?php
 
namespace Infoplus\Connect\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class InstallData implements InstallDataInterface
{
   private $eavSetupFactory;
   protected $_logger;
 
   public function __construct
   (
      EavSetupFactory $eavSetupFactory,
      \Psr\Log\LoggerInterface $logger
   )
   {
      $this->eavSetupFactory = $eavSetupFactory;
      $this->_logger = $logger;
   }
 
   public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
   {
      $this->_logger->info('Installing Infoplus custom attribute');

      $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
      $eavSetup->addAttribute(
         \Magento\Catalog\Model\Product::ENTITY,
         'fulfilled_by_infoplus',
         [
            'type' => 'int',
            'label' => 'Fulfilled By Infoplus',
            'input' => 'boolean',
            'required' => false,
            'sort_order' => 9000,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'Infoplus',
            'used_in_product_listing' => false,
            'visible_on_front' => false,
            'apply_to' => 'simple,configurable,bundle,grouped',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible' => true,
            'user_defined' => false,
            'default' => '0',
            'searchable' => false,
            'filterable' => true,
            'comparable' => false,
            'unique' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' =>  true
         ]
      );
   }
}
