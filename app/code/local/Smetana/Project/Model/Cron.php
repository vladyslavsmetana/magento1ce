<?php

/**
 * Distribution of orders
 *
 * Class Smetana_Project_Adminhtml_OrderController
 */
class Smetana_Project_Model_Cron
{
    /**
     * There are no matching orders parameter
     *
     * @var String
     */
    const NO_FITTED_ORDERS = 'nothing';

    /**
     * Orders Distribution process
     *
     * @param void
     *
     * @return Smetana_Project_Model_Cron
     */
    public function ordersDistribution(): Smetana_Project_Model_Cron
    {
        $email = '';

        while ($email != self::NO_FITTED_ORDERS) {
            $email = $this->assignOrders($email);
        }

        return $this;
    }

    /**
     * Assign Order to call-centre specialist
     *
     * @param string $email
     * @param Mage_Admin_Model_User|null
     *
     * @return string
     */
    private function assignOrders(string $email = ''): string
    {
        /** @var Mage_Admin_Model_User|null $userFromQueue */
        $userFromQueue = $this->getUserFromQueue();

        if (!is_null($userFromQueue)) {
            /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
            $orderCollection = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('order_initiator', ['null' => true])
                ->setOrder('entity_id', 'DESC');

            if ($email != '') {
                $orderCollection->addFieldToFilter('customer_email', ['eq' => $email]);
            }

            foreach ($orderCollection as $order) {
                if (!$this->checkTime(
                    explode(' ', $order->getData('created_at'))[1],
                    $userFromQueue->getData('orders_type')
                )) {
                    continue;
                }

                if ($this->checkProductType($order, $userFromQueue->getData('products_type'))) {
                    Mage::helper('smeproject')->addInitiatorToSpecificOrder($order, $userFromQueue->getData('user_id'));

                    return $order->getData('customer_email');
                }
            }

            if ($email != '') {
                $this->removeUserFromQueue($userFromQueue);
            }
        }

        return self::NO_FITTED_ORDERS;
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
        /** @var Mage_Admin_Model_Resource_User_Collection $userCollection */
        $userCollection = Mage::getModel('admin/user')
            ->getCollection()
            ->addFieldToFilter('need_order', ['notnull' => true])
            ->setOrder('need_order', 'ASC');

        if ($userCollection->getSize() > 0) {
            $userModel = $userCollection->getFirstItem();
        }

        return $userModel;
    }

    /**
     * Remove specific user from distribution queue
     *
     * @param Mage_Admin_Model_User $userFromQueue
     *
     * @return Smetana_Project_Model_Cron
     */
    private function removeUserFromQueue(Mage_Admin_Model_User $userFromQueue): Smetana_Project_Model_Cron
    {
        $userFromQueue->setData('need_order', null)->save();

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
        $allowed = true;

        switch ($timeType) {
            case 'night':
                $allowed = $createdAt > $twenty || $createdAt < $eight;
                break;
            case 'day':
                $allowed = $createdAt > $eight && $createdAt < $twenty;
                break;
        }

        return $allowed;
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

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $orderProducts]);

        foreach ($this->getFilterData($userProductType) as $filter) {
            $collection->addFieldToFilter('product_types', $filter);
        }

        return $collection->getSize() > 0;
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
        $filter = [];

        switch ($userProductType) {
            case $largeAppliances:
                $filter = [['in' => $largeAppliances]];
                break;
            case $smallAppliances:
                $filter = [
                    ['in' => $smallAppliances],
                    ['nin' => [$largeAppliances]],
                ];
                break;
            case $gadgets:
                $filter = [
                    ['in' => $gadgets],
                    ['nin' => [$largeAppliances, $smallAppliances]],
                ];
                break;
            default:
                $filter = [['nin' => [$largeAppliances, $smallAppliances, $gadgets]]];
        }

        return $filter;
    }
}
