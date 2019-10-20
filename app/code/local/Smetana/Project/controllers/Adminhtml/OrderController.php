<?php

/**
 * Orders distribution
 *
 * Class Smetana_Project_Adminhtml_OrderController
 */
class Smetana_Project_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Assign Order to call-centre specialist
     *
     * @param string $email
     *
     * @return Smetana_Project_Adminhtml_OrderController
     */
    public function getOrderAction($email = ''): Smetana_Project_Adminhtml_OrderController
    {
        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('order_initiator', ['null' => true])
            ->setOrder('entity_id', 'DESC');

        if ($email != '') {
            $orderCollection->addFieldToFilter('customer_email', ['eq' => $email]);
        }

        $adminUser =  Mage::helper('smeproject')->getAdminUser();

        foreach ($orderCollection as $order) {
            if (!$this->checkTime(
                explode(' ', $order->getData('created_at'))[1],
                $adminUser->getData('orders_type')
            )) {
                continue;
            }

            if ($this->checkProductType($order, $adminUser->getData('products_type'))) {
                Mage::helper('smeproject')->addOrderInitiator($order);
                $this->getOrderAction($order->getData('customer_email'));

                return $this;
            }
        }
        Mage::app()->getResponse()->setRedirect($this->getOrderGridUrl()) ->sendResponse();

        return $this;
    }

    /**
     * Compare provided time with allowed
     *
     * @param string $createdAt
     * @param string $timeType
     *
     * @return bool
     */
    private function checkTime(string $createdAt, string $timeType = ''): bool
    {
        $eight = '08:00:00';
        $twenty = '20:00:00';

        switch ($timeType) {
            case 'night':
                return $createdAt > $twenty || $createdAt < $eight;
            case 'day':
                return $createdAt > $eight && $createdAt < $twenty;
            default:
                return true;
        }
    }

    /**
     * Check product type according to user data
     *
     * @param Mage_Sales_Model_Order $orderModel
     * @param string $userProductType
     *
     * @return bool
     */
    private function checkProductType(Mage_Sales_Model_Order $orderModel, string $userProductType = ''): bool
    {
        $orderProducts = [];
        foreach ($orderModel->getAllItems() as $item) {
            $orderProducts[] = $item->getData('product_id');
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $orderProducts]);

        foreach ($this->getFilterData($userProductType) as $filter) {
            $collection->addFieldToFilter('product_types', $filter);
        }

        if ($collection->getSize() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get data to filter Collection
     *
     * @param string $userProductType
     *
     * @return array
     */
    private function getFilterData(string $userProductType): array
    {
        $largeAppliances = 'large_appliances';
        $smallAppliances = 'small_appliances';
        $gadgets = 'gadgets';

        switch ($userProductType) {
            case $largeAppliances:
                return [['in' => $largeAppliances]];
            case $smallAppliances:
                return [
                    ['in' => $smallAppliances],
                    ['nin' => [$largeAppliances]],
                ];
            case $gadgets:
                return [
                    ['in' => $gadgets],
                    ['nin' => [$largeAppliances, $smallAppliances]],
                ];
            default:
                return [['nin' => [$largeAppliances, $smallAppliances, $gadgets]]];
        }
    }

    /**
     * Get Order grid url
     *
     * @param void
     *
     * @return string
     */
    private function getOrderGridUrl(): string
    {
        return Mage::getUrl('adminhtml/sales_order/index');
    }

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
        Mage::app()->getResponse()->setRedirect($this->getOrderGridUrl()) ->sendResponse();

        return $this;
    }
}
