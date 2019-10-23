<?php

/**
 * Smetana Project Observer
 *
 * Class Smetana_Project_Model_Observer
 */
class Smetana_Project_Model_Observer
{
    /**
     * Add Get Order button to Order Grid page
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Smetana_Project_Model_Observer
     */
    public function addGetOrderButton(Varien_Event_Observer $observer): Smetana_Project_Model_Observer
    {
        $container = $observer->getBlock();

        if (
            null !== $container
            && $container->getData('type') == 'adminhtml/sales_order'
            && Smetana_Project_Helper_Data::getAdminUser('role') == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME
        ) {
            $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter(
                    'order_initiator',
                    ['eq' => Smetana_Project_Helper_Data::getAdminUser()->getData('user_id')]
                );

            if (!in_array('pending', $collection->getColumnValues('status'))) {
                $isDisabled = Mage::app()->getRequest()->getParam('disabled') ?? false;
                $data = [
                    'label' => $isDisabled ? 'Ожидание заказа' : 'Получить заказ',
                    'disabled' => $isDisabled,
                    'onclick' => 'disableElements(\'my-button\');setLocation(\'' . Mage::helper('adminhtml')
                            ->getUrl('smetana_project_admin/adminhtml_order/setqueue') . '\')',
                ];

                $container->addButton('get_order_button', $data, 0, 1);
            }
        }

        return $this;
    }

    /**
     * Reload page while waiting for order
     *
     * @param void
     *
     * @return Smetana_Project_Model_Observer
     */
    public function pageReload(): Smetana_Project_Model_Observer
    {
        if (Mage::app()->getRequest()->getParam('disabled')) {
            echo '<script type="text/javascript">','
            function runReload () {
            setInterval(\'refreshPage()\', 5000);
            }
            
            function refreshPage() {
                location.reload();
            }
            
            runReload();', '</script>';

            if (Mage::registry('availableOrders')->getSize() > 0) {
                $collectionQueue = Mage::getModel('smetana_project_model/queue')
                    ->getCollection()
                    ->addFieldToFilter('user_id', ['eq' => Smetana_Project_Helper_Data::getAdminUser()->getId()]);

                foreach ($collectionQueue as $queue) {
                    $queue->delete();
                }

                Mage::app()->getResponse()->setRedirect(Mage::helper('smeproject')->getOrderGridUrl())->sendResponse();
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
        if (Smetana_Project_Helper_Data::getAdminUser('role') == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME) {
            $orderId = $observer->getData('controller_action')->getRequest()->getParam('order_id');
            $orderModel = Mage::getModel('sales/order')->load($orderId);
            Mage::helper('smeproject')->addOrderInitiator($orderModel);
        }

        return $this;
    }
}
