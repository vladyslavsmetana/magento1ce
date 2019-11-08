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
    public static function getAdminUser(): Mage_Admin_Model_User
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
        /** @var Mage_Admin_Model_User $user */
        $user = static::getAdminUser();

        return $user->getRole()->getRoleName();
    }

    /**
     * Get user orders button data
     *
     * @param void
     *
     * @return array
     */
    public function getUserOrders(): array
    {
        $data = [];
        if (static::getUserRoleName() == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME) {
            /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter(
                    'order_initiator',
                    ['eq' => static::getAdminUser()->getData('user_id')]
                );

            if (!in_array('pending', $collection->getColumnValues('status'))) {
                $isButtonDisabled = $this->isButtonDisabled();
                $data = [
                    'label' => $isButtonDisabled
                        ? __('Waiting for the order')
                        : __('Get Order'),
                    'disabled' => $isButtonDisabled,
                    'onclick' => 'disableElements(\'my-button\');setLocation(\'' . Mage::helper('adminhtml')
                            ->getUrl('smetana_project_admin/adminhtml_order/setqueue') . '\')',
                ];
            }
        }

        return $data;
    }

    /**
     * Get Order grid url
     *
     * @return string
     */
    public function getOrderGridUrl(): string
    {
        return Mage::getUrl(Smetana_Project_Block_Options::PATH_TO_ORDER_GRID);
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
