<?php

$installer = $this;
$installer->startSetup();

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$defaultAttributeSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();
$attributeGroups = Mage::getModel('eav/entity_attribute_group')
    ->getResourceCollection()
    ->setAttributeSetFilter($defaultAttributeSetId)
    ->setSortOrder()
    ->load();

$entityTypeId = 'catalog_product';

foreach ($attributeGroups as $group) {
    $groupName = $group->getData('attribute_group_name');
    $groupId = $group->getData('attribute_group_id');
    $installer->addAttributeGroup(
        $entityTypeId,
        Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET,
        $groupName
    );

    $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
        ->setAttributeGroupFilter($groupId)
        ->addVisibleFilter()
        ->checkConfigurableProducts()
        ->load();

    if ($attributes->getSize() > 0) {
        foreach ($attributes->getItems() as $attribute) {
            $installer->addAttributeToSet(
                $entityTypeId,
                $installer->getAttributeSetId(
                    $entityTypeId,
                    Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET
                ),
                $installer->getAttributeGroupId(
                    $entityTypeId,
                    Smetana_Project_Block_Options::PRODUCT_ATTRIBUTE_SET,
                    $groupName
                ),
                $attribute->getAttributeId()
            );
        }
    }

}

$installer->endSetup();
