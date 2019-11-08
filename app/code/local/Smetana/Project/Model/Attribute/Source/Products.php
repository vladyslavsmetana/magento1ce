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
        /** @var Smetana_Project_Helper_Data $helper */
        $helper = Mage::helper('smeproject');
        if (!$this->_options) {
            $this->_options = [
                'non-selected'     => $helper->__('Not specified'),
                'large_appliances' => $helper->__('Large home appliances'),
                'small_appliances' => $helper->__('Small household appliances'),
                'gadgets'          => $helper->__('Gadgets'),
            ];
        }

        return $this->_options;
    }
}
