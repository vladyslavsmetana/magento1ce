<?php

/**
 * Initiators config data model
 *
 * Class Smetana_Project_Model_Order_Config
 */
class Smetana_Project_Model_Order_Config
{
    /**
     * Retrieve all allowed initiators
     *
     * @param void
     *
     * @return array
     */
    public function getInitiators(): array
    {
        $options = [];
        /** @var Mage_Admin_Model_Resource_User_Collection $users */
        $users = Mage::getModel('admin/user')->getCollection();

        foreach ($users as $user) {
            if ($user->getRole()->getData('role_name') == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME) {
                $options[$user->getId()] = $user->getData('username');
            }
        }

        return $options;
    }
}
