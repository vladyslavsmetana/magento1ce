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

$adminRoles = [Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME, Smetana_Project_Block_Options::COORDINATOR_ROLE_NAME];

foreach ($adminRoles as $role) {
    Mage::getModel('admin/roles')
        ->setData(['name' => $role, 'role_type' => 'G'])
        ->save();
}

$installer->endSetup();
