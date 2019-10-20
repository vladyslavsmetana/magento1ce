<?php

/**
 * Smetana Project Observer
 *
 * Class Smetana_Project_Model_Observer
 */
class Smetana_Project_Model_Observer
{
    /**
     * Join email data to order grid
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Mage_Sales_Model_Resource_Order_Grid_Collection
     */
    public function addEmailData(Varien_Event_Observer $observer): Mage_Sales_Model_Resource_Order_Grid_Collection
    {
        $collection = $observer->getData('order_grid_collection');
        $collection->getSelect()->join(
            ['order' => $collection->getTable('sales/order')],
            'order.entity_id=main_table.entity_id',
            ['customer_email' => 'customer_email']
        );

        return $collection;
    }

    /**
     * Add Get Order button to Order Grid page
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Smetana_Project_Model_Observer
     */
    public function addGetOrderButton(Varien_Event_Observer $observer): Smetana_Project_Model_Observer
    {
        $adminUser =  Mage::helper('smeproject')->getAdminUser();
        $container = $observer->getBlock();

        if (
            null !== $container
            && $container->getData('type') == 'adminhtml/sales_order'
            && $adminUser->getData('callcentre_role') == 'specialist'
        ) {
            $collection = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('order_initiator', ['eq' => $adminUser->getData('user_id')]);
            if (!in_array('pending', $collection->getColumnValues('status'))) {
                $data = [
                    'label' => 'Получить заказ',
                    'onclick' => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('smetana_project_admin/adminhtml_order/getorder') . '\')',
                ];
                $container->addButton('get_order_button', $data, 0, 1);
            }
        }

        return $this;
    }

    /**
     * Add call-centre Initiator before view Order
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Smetana_Project_Model_Observer
     */
    public function addInitiatorWhenViewOrder(Varien_Event_Observer $observer): Smetana_Project_Model_Observer
    {
        if (Mage::helper('smeproject')->getAdminUser()->getData('callcentre_role') == 'specialist') {
            $orderId = $observer->getData('controller_action')->getRequest()->getParam('order_id');
            $orderModel = Mage::getModel('sales/order')->load($orderId);
            Mage::helper('smeproject')->addOrderInitiator($orderModel);
        }

        return $this;
    }
}
