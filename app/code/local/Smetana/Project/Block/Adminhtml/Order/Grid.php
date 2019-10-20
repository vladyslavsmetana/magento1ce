<?php

/**
 * Change order grid
 *
 * Class Smetana_Project_Block_Adminhtml_Order_Grid
 */
class Smetana_Project_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    /**
     * Configure collection according to user data
     *
     * @return void
     */
    public function setCollection(): void
    {
        $adminUser = Mage::helper('smeproject')->getAdminUser();
        $filter = [];
        switch ($adminUser->getData('callcentre_role')) {
            case 'coordinator':
                $filter = ['notnull' => true];
                break;
            case 'specialist':
                $filter = ['eq' => $adminUser->getData('user_id')];
                break;
        }

        $collection = Mage::getModel('sales/order')->getCollection();
        if (!empty($filter)) {
            $collection->addFieldToFilter('order_initiator', $filter);
        }

        $this->_collection = $collection;
    }

    /**
     * Search by email column
     *
     * @param array $data
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Grid
     */
    protected function _setFilterValues(array $data): Mage_Adminhtml_Block_Sales_Order_Grid
    {
        if (
            Mage::helper('smeproject')->getAdminUser()->getData('callcentre_role') == 'specialist'
            && array_key_exists('customer_email', $data)
        ) {
            $collection = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_email', ['eq' => $data['customer_email']]);
            parent::setCollection($collection);
        }

        return parent::_setFilterValues($data);
    }

    /**
     * Change order grid columns
     *
     * @param void
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Grid
     * @throws Exception
     */
    protected function _prepareColumns(): Mage_Adminhtml_Block_Sales_Order_Grid
    {
        $this->addColumnAfter(
            'customer_email',
            [
                'header' => Mage::helper('sales')->__('Email'),
                'index'  => 'customer_email',
            ],
            'status'
        );

        $this->addColumnAfter(
            'order_primary_initiator',
            [
                'header'   => Mage::helper('sales')->__('Инициатор'),
                'index'    => 'order_primary_initiator',
                'filter'    => false,
                'sortable' => false,
                'renderer' => 'Smetana_Project_Block_Adminhtml_Order_Grid_Renderer',
            ],
            'customer_email'
        );

        $this->addColumnAfter(
            'order_initiator',
            [
                'header'   => Mage::helper('sales')->__('Первичный инициатор'),
                'index'    => 'order_initiator',
                'filter'    => false,
                'sortable' => false,
                'renderer' => 'Smetana_Project_Block_Adminhtml_Order_Grid_Renderer',
            ],
            'order_primary_initiator'
        );

        $parent = parent::_prepareColumns();

        if (Mage::helper('smeproject')->getAdminUser()->getData('callcentre_role') == 'coordinator') {
            $actionColumn = $this->getColumn('action')->getData();
            $actionColumn['actions'][] = [
                'caption' => Mage::helper('sales')->__('Очистить инициатора'),
                'url'     => ['base' => Smetana_Project_Block_Options::PATH_TO_REMOVE_INITIATOR],
                'field'   => 'order_id',
                'data-column' => 'action',
            ];
            $this->addColumn('action', $actionColumn);
        }

        return $parent;
    }

    /**
     * Add new massaction element
     *
     * @param void
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Grid
     */
    protected function _prepareMassaction(): Mage_Adminhtml_Block_Sales_Order_Grid
    {
        $parent = parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('initiator_clean', array(
            'label'=> Mage::helper('sales')->__('Очистить инициатора'),
            'url'  => $this->getUrl(Smetana_Project_Block_Options::PATH_TO_REMOVE_INITIATOR),
        ));

        return $parent;
    }
}
