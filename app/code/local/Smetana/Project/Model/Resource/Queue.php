<?php

/**
 * Queue Resource class
 *
 * Class Smetana_Project_Model_Resource_Queue
 */
class Smetana_Project_Model_Resource_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('smetana_project_model/queue', 'id');
    }
}
