<?php
/**C:\xampp\htdocs\cisco\app\code\Training4\Warranty\Setup\InstallData.php
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Training4\Warranty\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


/**
 * Defines the service layer  for getting the Vendor informaion from vendor Id.
 * Save new Vendor
 * Get List of All the Vendors
 * Get associated productIds of a vendor
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    
	
	protected $_setFactory;

	protected $_setAttribute;
	
	
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory , \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
		$this->_setFactory = $setFactory;
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
      
		
		/** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
		$entityTypeId = $eavSetup->getEntityTypeId('catalog_product');
		
		
		$this->_setFactory->create()->setEntityTypeId($entityTypeId)
		->setAttributeSetName(trim('Gear'))
		 ->save()
		 ->initFromSkeleton($eavSetup->getAttributeSetId('catalog_product','default'))
		 ->save();

		
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'warranty',
            [
                'type' => 'text',
				 'attribute_set' => 'Gear',
				  'group' => 'General',
                'backend' => '',
                'frontend' => '',
                'label' => 'Warranty',
                'input' => '',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }
    
}