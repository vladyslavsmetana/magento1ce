<?php

/**
 * Sales Order view rewrite
 *
 * Class Smetana_Project_Block_Adminhtml_Order_View
 */
class Smetana_Project_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{
    /**
     * Add button to Order view page
     *
     * @oaram void
     */
    public function __construct()
    {
        parent::__construct();
        $currentOrder = 'current_order';

        if (
            Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::COORDINATOR_ROLE_NAME
            && !is_null(Mage::registry($currentOrder))
            && !is_null(Mage::registry($currentOrder)->getData('order_initiator'))
        ) {
            $data = [
                'label' => Mage::helper('smeproject')->__('Clean initiator'),
                'onclick' => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl(
                    Smetana_Project_Block_Options::PATH_TO_REMOVE_INITIATOR,
                    ['order_id' => Mage::registry($currentOrder)->getId()]
                ) . '\')',
            ];

            $this->_addButton('clean_initiator_button', $data, 0, 1);
        }
    }
}
