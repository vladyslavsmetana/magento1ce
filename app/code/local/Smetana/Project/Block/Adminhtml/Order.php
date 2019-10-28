<?php

/**
 * Sales Order Block rewrite
 *
 * Class Smetana_Project_Block_Adminhtml_Order
 */
class Smetana_Project_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Sales_Order
{
    /**
     * Add button to Order grid
     *
     * @param void
     */
    public function __construct()
    {
        parent::__construct();

        if (Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME) {
            /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter(
                    'order_initiator',
                    ['eq' => Smetana_Project_Helper_Data::getAdminUser()->getData('user_id')]
                );

            if (!in_array('pending', $collection->getColumnValues('status'))) {
                $helper = Mage::helper('smeproject');
                $data = [
                    'label' => $helper->isButtonDisabled()
                        ? $helper->__('Waiting for the order')
                        : $helper->__('Get Order'),
                    'disabled' => $helper->isButtonDisabled(),
                    'onclick' => 'disableElements(\'my-button\');setLocation(\'' . Mage::helper('adminhtml')
                            ->getUrl('smetana_project_admin/adminhtml_order/setqueue') . '\')',
                ];

                $this->addButton('get_order_button', $data, 0, 1);
            }
        }
    }
}
