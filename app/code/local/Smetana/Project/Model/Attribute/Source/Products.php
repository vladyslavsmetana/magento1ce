<?php

/**
 * Smetana Project config data model
 *
 * Class Smetana_Project_Model_Attribute_Source_Products
 */
class Smetana_Project_Model_Attribute_Source_Products extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve product options
     *
     * @param void
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            $this->_options = [
                'non-selected'     => Mage::helper('adminhtml')->__('Не указан'),
                'large_appliances' => Mage::helper('adminhtml')->__('Крупная бытовая техника'),
                'small_appliances' => Mage::helper('adminhtml')->__('Мелкая бытовая техника'),
                'gadgets'          => Mage::helper('adminhtml')->__('Гаджеты'),
            ];
        }

        return $this->_options;
    }
}
