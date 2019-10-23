<?php

$installer = $this;
$installer->startSetup();

$installer->run('DROP TABLE IF EXISTS smetana_project_queue;');
$table = $installer->getConnection()
    ->newTable('smetana_project_queue')
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'auto_increment' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ]
    )
    ->addColumn(
        'user_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'unique'   => true,
            'nullable' => false,
        ]
    );

$installer->getConnection()->createTable($table);
$installer->endSetup();
