<?php

/**
 * Smetana Project Helper class
 *
 * Class Smetana_Project_Helper_Data
 */
class Smetana_Project_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Current Admin user Model
     *
     * @var Mage_Admin_Model_User
     */
    static $currentAdminUser;

    /**
     * Retrieve current Admin user Model
     *
     * @param void
     *
     * @return mixed
     */
    public static function getAdminUser()
    {
        if (null === static::$currentAdminUser) {
            static::$currentAdminUser = Mage::getSingleton('admin/session')->getData('user');
        }

        return static::$currentAdminUser;
    }

    /**
     * Retrieve Admin user Role name
     *
     * @param void
     *
     * @return string
     */
    public function getUserRoleName(): string
    {
        return Mage::getModel('admin/role')
            ->load(static::getAdminUser()->getRoles()[0])
            ->getData('role_name');
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
     * Check button disabled param
     *
     * @param void
     *
     * @return bool
     */
    public function isButtonDisabled(): bool
    {
        return Mage::app()->getRequest()->getParam('disabled') ?? false;
    }

    /**
     * Add call-centre Initiator to order Model
     *
     * @param Mage_Sales_Model_Order $orderModel
     * @param $userId
     *
     * @return Smetana_Project_Helper_Data
     */
    public function addInitiatorToSpecificOrder(Mage_Sales_Model_Order $orderModel, $userId = null): Smetana_Project_Helper_Data
    {
        $columns = ['order_initiator'];

        if (null === $orderModel->getData('order_primary_initiator')) {
            $columns[] = 'order_primary_initiator';
        }

        foreach ($columns as $column) {
            $orderModel->setData($column, $userId ?? static::getAdminUser()->getData('user_id'))->save();
        }

        return $this;
    }
}
