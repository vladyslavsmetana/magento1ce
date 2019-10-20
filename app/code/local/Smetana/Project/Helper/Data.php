<?php

/**
 * Smetana Project Helper class
 *
 * Class Smetana_Project_Helper_Data
 */
class Smetana_Project_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Admin user model
     *
     * @var Mage_Admin_Model_User
     */
    static $adminUser;

    /**
     * Retrieve Admin user model
     *
     * @param void
     *
     * @return Mage_Admin_Model_User
     */
    public static function getAdminUser(): Mage_Admin_Model_User
    {
        if (!static::$adminUser) {
            static::$adminUser = Mage::getSingleton('admin/session')->getData('user');
        }

        return static::$adminUser;
    }

    /**
     * Add call-centre Initiator to order Model
     *
     * @param Mage_Sales_Model_Order $orderModel
     *
     * @return Smetana_Project_Helper_Data
     */
    public function addOrderInitiator(Mage_Sales_Model_Order $orderModel): Smetana_Project_Helper_Data
    {
        $columns = ['order_initiator'];

        if (null === $orderModel->getData('order_primary_initiator')) {
            $columns[] = 'order_primary_initiator';
        }

        foreach ($columns as $column) {
            $orderModel->setData($column, static::getAdminUser()->getData('user_id'))->save();
        }

        return $this;
    }
}
