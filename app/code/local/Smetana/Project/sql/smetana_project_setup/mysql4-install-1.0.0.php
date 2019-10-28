<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$attributeNames = Mage::getModel('eav/entity_attribute_set')
    ->getCollection()
    ->getColumnValues('attribute_set_name');

if (!in_array(Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET, $attributeNames)) {
    $entityTypeId = Mage::getModel('catalog/product')
        ->getResource()
        ->getEntityType()
        ->getId();

    /** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
    $attributeSet = Mage::getModel('eav/entity_attribute_set')
        ->setData([
            'entity_type_id' => $entityTypeId,
            'attribute_set_name' => Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET
        ])
        ->save();

    $attributeSet->initFromSkeleton($entityTypeId)->save();
    $entityTypeId = 'catalog_product';

    $installer->addAttribute(
        $entityTypeId,
        'product_types',
        [
            'attribute_set' => Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET,
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'sort_order' => '0',
            'label' => Mage::helper('smeproject')->__('Type of Product'),
            'input' => 'select',
            'source' => 'smetana_project_model/attribute_source_products',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '0',
            'searchable' => false,
            'filterable' => true,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
        ]
    );

    $installer->addAttributeToSet(
        $entityTypeId,
        Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET,
        'General',
        'product_types'
    );

}

$installer->endSetup();
