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
        $helper = Mage::helper('adminhtml');
        if (!$this->_options) {
            $this->_options = [
                'non-selected'     => $helper->__('Не указан'),
                'large_appliances' => $helper->__('Крупная бытовая техника'),
                'small_appliances' => $helper->__('Мелкая бытовая техника'),
                'gadgets'          => $helper->__('Гаджеты'),
            ];
        }

        return $this->_options;
    }
}
