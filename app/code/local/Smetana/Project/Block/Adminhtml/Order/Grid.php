<?php

/**
 * Change order grid
 *
 * Class Smetana_Project_Block_Adminhtml_Order_Grid
 */
class Smetana_Project_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    /**
     * Path to Admin user repository model
     *
     * @var String
     */
    const PATH_TO_USER_REPOSITORY = 'smetana_project_model/renderer_user_repository';

    /**
     * Configure collection according to user data
     *
     * @return void
     * @throws Mage_Core_Exception
     */
    public function setCollection(): void
    {
        $filter = [];
        switch (Smetana_Project_Helper_Data::getUserRoleName()) {
            case Smetana_Project_Block_Options::COORDINATOR_ROLE_NAME:
                $filter = ['notnull' => true];
                break;
            case Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME:
                $filter = ['eq' => Smetana_Project_Helper_Data::getAdminUser()->getData('user_id')];
                break;
        }

        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        if (!empty($filter)) {
            $collection->addFieldToFilter('order_initiator', $filter);
        }

        $collection = $this->joinUsername($collection);
        Mage::register('availableOrders', $collection);

        $this->_collection = $collection;
    }

    /**
     * Set filter by email column
     *
     * @param array $data
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Grid
     */
    protected function _setFilterValues(array $data): Mage_Adminhtml_Block_Sales_Order_Grid
    {
        if (
            Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME
            && array_key_exists('customer_email', $data)
        ) {
            /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_email', ['eq' => $data['customer_email']]);
            $collection = $this->joinUsername($collection);

            parent::setCollection($collection);
        }

        return parent::_setFilterValues($data);
    }

    /**
     * Join username column to orders Collection
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    private function joinUsername(Mage_Sales_Model_Resource_Order_Collection $collection): Mage_Sales_Model_Resource_Order_Collection
    {
        $collection->getSelect()
            ->joinLeft('admin_user', 'user_id=order_initiator', ['order_initiator' => 'username']);

        return $collection;
    }

    /**
     * Add columns to order grid
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
                'header'   => Mage::helper('sales')->__('Primary Initiator'),
                'index'    => 'order_primary_initiator',
                'type'  => 'options',
                'sortable' => false,
                'options' => Mage::getSingleton(self::PATH_TO_USER_REPOSITORY)->getInitiators(),
                'renderer' => 'Smetana_Project_Block_Adminhtml_Order_Grid_Renderer',
            ],
            'customer_email'
        );

        $this->addColumnAfter(
            'order_initiator',
            [
                'header'   => Mage::helper('sales')->__('Initiator'),
                'index'    => 'order_initiator',
                'type'  => 'options',
                'sortable' => false,
                'options' => Mage::getSingleton(self::PATH_TO_USER_REPOSITORY)->getInitiators(),
            ],
            'order_primary_initiator'
        );

        $parent = parent::_prepareColumns();

        if (Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::COORDINATOR_ROLE_NAME) {
            $actionColumn = $this->getColumn('action')->getData();
            $actionColumn['actions'][] = [
                'caption' => Mage::helper('sales')->__('Clean initiator'),
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
        if (Smetana_Project_Helper_Data::getUserRoleName() == Smetana_Project_Block_Options::COORDINATOR_ROLE_NAME) {
            $this->getMassactionBlock()->addItem(
                'initiator_clean',
                [
                    'label' => Mage::helper('sales')->__('Clean initiator'),
                    'url' => $this->getUrl(Smetana_Project_Block_Options::PATH_TO_REMOVE_INITIATOR),
                ]
            );
        }

        return $parent;
    }
}
