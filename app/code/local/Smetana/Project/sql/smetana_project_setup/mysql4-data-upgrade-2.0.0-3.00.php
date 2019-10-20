<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('admin/user'),
    'orders_type',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 256,
        'nullable' => true,
        'default' => null,
        'comment' => 'Тип заказов',
    ]
);

$installer->getConnection()->addColumn(
    $installer->getTable('admin/user'),
    'products_type',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 256,
        'nullable' => true,
        'default' => null,
        'comment' => 'Тип товаров',
    ]
);

$installer->getConnection()->addColumn(
    $installer->getTable('admin/user'),
    'callcentre_role',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 256,
        'nullable' => true,
        'default' => null,
        'comment' => 'Роль в колл-центре',
    ]
);

$installer->endSetup();
