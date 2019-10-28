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
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
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
        $queueColumnName = 'need_order';

        $lastInQueue = max(
            Mage::getModel('admin/user')
                ->getCollection()
                ->getColumnValues($queueColumnName)
            ) ?? 0;

        Smetana_Project_Helper_Data::getAdminUser()
            ->setData($queueColumnName, $lastInQueue + 1)->save();

        Mage::app()->getResponse()
            ->setRedirect(Mage::helper('smeproject')->getOrderGridUrl(['disabled' => true]))
            ->sendResponse();

        return $this;
    }
}
