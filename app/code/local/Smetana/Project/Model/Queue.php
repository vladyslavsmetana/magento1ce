<?php

/**
 * Queue Model class
 *
 * Class Smetana_Project_Model_Queue
 */
class Smetana_Project_Model_Queue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('smetana_project_model/queue');
    }
}
