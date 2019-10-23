<?php

/**
 * Work with order distribution
 *
 * Class Smetana_Project_Adminhtml_OrderController
 */
class Smetana_Project_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Clean order initiator
     *
     * @param void
     *
     * @return Smetana_Project_Adminhtml_OrderController
     */
    public function cleanInitiatorAction(): Smetana_Project_Adminhtml_OrderController
    {
        $requestParams = $this->getRequest()->getParams();
        $orderId = $requestParams['order_id']  ?? $requestParams['order_ids'];

        if (!is_array($orderId)) {
            $orderId = [$orderId];
        }
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('entity_id', ['in' => $orderId]);

        foreach ($collection as $order) {
            $order->setData('order_initiator', null)->save();
        }
        Mage::app()->getResponse()->setRedirect(Mage::helper('smeproject')->getOrderGridUrl()) ->sendResponse();

        return $this;
    }

    /**
     * Set distribution queue
     *
     * @param void
     *
     * @return Smetana_Project_Adminhtml_OrderController
     */
    public function setQueueAction(): Smetana_Project_Adminhtml_OrderController
    {
        Mage::getModel('smetana_project_model/queue')
            ->setData('user_id', Smetana_Project_Helper_Data::getAdminUser()->getId())
            ->save();
        Mage::app()->getResponse()
            ->setRedirect(Mage::helper('smeproject')->getOrderGridUrl(['disabled' => true]))
            ->sendResponse();

        return $this;
    }
}
