<?php

/**
 * Sales Order Block rewrite
 *
 * Class Smetana_Project_Block_Adminhtml_Order
 */
class Smetana_Project_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Sales_Order
{
    /**
     * Add button to Order grid
     *
     * @param void
     */
    public function __construct()
    {
        parent::__construct();

        $buttonData = Mage::helper('smeproject')->getUserOrders();
        if (!empty($buttonData)) {
            $this->addButton('get_order_button', $buttonData, 0, 1);
        }
    }
}
