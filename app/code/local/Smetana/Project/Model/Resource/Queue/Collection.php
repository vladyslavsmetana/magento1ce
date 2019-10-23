<?php

/**
 * Queue Collection class
 *
 * Class Smetana_Project_Model_Resource_Queue_Collection
 */
class Smetana_Project_Model_Resource_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('smetana_project_model/queue');
    }
}
