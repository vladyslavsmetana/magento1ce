<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'),
    'order_initiator',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Order Initiator',
    ]
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'),
    'order_primary_initiator',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Order Primary Initiator',
    ]
);

$installer->endSetup();
