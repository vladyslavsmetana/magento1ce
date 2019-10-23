<?php

/**
 * Smetana Project Helper class
 *
 * Class Smetana_Project_Helper_Data
 */
class Smetana_Project_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Current Admin user model
     *
     * @var Mage_Admin_Model_User
     */
    static $currentAdminUser;

    /**
     * Retrieve current Admin user data
     *
     * @param void
     *
     * @return mixed
     */
    public static function getAdminUser($param = '')
    {
        if (null === static::$currentAdminUser) {
            static::$currentAdminUser = Mage::getSingleton('admin/session')->getData('user');
        }

        if ($param == 'role') {
            return Mage::getModel('admin/role')
                ->load(static::$currentAdminUser->getRoles()[0])
                ->getData('role_name');
        }

        return static::$currentAdminUser;
    }

    /**
     * Get Order grid url
     *
     * @param void
     *
     * @return string
     */
    public function getOrderGridUrl($params = []): string
    {
        return Mage::getUrl('adminhtml/sales_order/index', $params);
    }

    /**
     * Add call-centre Initiator to order Model
     *
     * @param Mage_Sales_Model_Order $orderModel
     * @param $userId
     *
     * @return Smetana_Project_Helper_Data
     */
    public function addOrderInitiator(Mage_Sales_Model_Order $orderModel, $userId = null): Smetana_Project_Helper_Data
    {
        $columns = ['order_initiator'];

        if (null === $orderModel->getData('order_primary_initiator')) {
            $columns[] = 'order_primary_initiator';
        }

        foreach ($columns as $column) {
            $orderModel->setData($column, $userId ?? static::getAdminUser()->getData('user_id'))->save();
        }

        if (null !== $userId) {
            $queueCollection = Mage::getModel('smetana_project_model/queue')
                ->getCollection()
                ->addFieldToFilter('user_id', ['eq' => $userId]);

            foreach ($queueCollection as $queue) {
                $queue->delete();
            }
        }

        return $this;
    }
}
