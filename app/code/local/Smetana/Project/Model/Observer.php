<?php

/**
 * Smetana Project Observer
 *
 * Class Smetana_Project_Model_Observer
 */
class Smetana_Project_Model_Observer
{
    /**
     * Skip order page reload while waiting for order
     *
     * @param void
     *
     * @return Smetana_Project_Model_Observer
     */
    public function skipPageReload(): Smetana_Project_Model_Observer
    {
        if (
            Mage::helper('smeproject')->isButtonDisabled()
            && Mage::registry('availableOrders')->getSize() > 0
        ) {
            Mage::app()->getResponse()->setRedirect(Mage::helper('smeproject')->getOrderGridUrl())->sendResponse();
        }

        return $this;
    }

    /**
     * Add call-centre Initiator before view Order
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Smetana_Project_Model_Observer
     */
    public function addInitiatorWhenViewOrder(Varien_Event_Observer $observer): Smetana_Project_Model_Observer
    {
        if (Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME) {
            $orderId = $observer->getData('controller_action')->getRequest()->getParam('order_id');
            /** @var Mage_Sales_Model_Order $orderModel */
            $orderModel = Mage::getModel('sales/order')->load($orderId);
            Mage::helper('smeproject')->addInitiatorToSpecificOrder($orderModel);
        }

        return $this;
    }
}
