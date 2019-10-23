<?php

/**
 * Distribution of orders
 *
 * Class Smetana_Project_Adminhtml_OrderController
 */
class Smetana_Project_Model_Cron
{
    /**
     * Assign Order to call-centre specialist
     *
     * @param string $email
     * @param Mage_Admin_Model_User|null
     *
     * @return Smetana_Project_Model_Cron
     */
    public function getOrders($email = '', $user = null): Smetana_Project_Model_Cron
    {
        $userModel = $user ?? $this->getUserFromQueue();

        if (null !== $userModel) {
            $orderCollection = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('order_initiator', ['null' => true])
                ->setOrder('entity_id', 'DESC');

            if ($email != '') {
                $orderCollection->addFieldToFilter('customer_email', ['eq' => $email]);
            }

            foreach ($orderCollection as $order) {
                if (!$this->checkTime(
                    explode(' ', $order->getData('created_at'))[1],
                    $userModel->getData('orders_type')
                )) {
                    continue;
                }

                if ($this->checkProductType($order, $userModel->getData('products_type'))) {
                    Mage::helper('smeproject')->addOrderInitiator($order, $userModel->getData('user_id'));
                    $this->getOrders($order->getData('customer_email'), $userModel);

                    return $this;
                }
            }
            Mage::app()->getResponse()->setRedirect(Mage::helper('smeproject')->getOrderGridUrl())->sendResponse();
        }

        return $this;
    }

    /**
     * Get User Model from queue
     *
     * @param void
     *
     * @return Mage_Admin_Model_User|null
     */
    private function getUserFromQueue()
    {
        $userModel = null;
        $queueCollection = Mage::getModel('smetana_project_model/queue')
            ->getCollection();

        if ($queueCollection->getSize() > 0) {
            $userId = $queueCollection->getFirstItem()
                ->getData('user_id');
            $userModel = Mage::getModel('admin/user')->load($userId);
        }

        return $userModel;
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
}
