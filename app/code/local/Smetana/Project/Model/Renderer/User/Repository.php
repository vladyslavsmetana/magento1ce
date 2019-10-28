<?php

/**
 * Admin user repository class
 *
 * Class Smetana_Project_Model_Renderer_User_Repository
 */
class Smetana_Project_Model_Renderer_User_Repository
{
    /**
     * Admin user Collection
     *
     * @var Mage_Admin_Model_User
     */
    private $adminUserCollection;

    /**
     * Get Admin user Model by ID
     *
     * @param int $id
     *
     * @return void|Mage_Admin_Model_User
     */
    public function getAdminUserById(int $id)
    {
        foreach ($this->getAdminUserCollection() as $adminUser) {
            if ($adminUser->getId() == $id) {
                return $adminUser;
            }
        }
    }

    /**
     * Get Admin user Collection
     *
     * @param void
     *
     * @return Mage_Admin_Model_Resource_User_Collection
     */
    private function getAdminUserCollection(): Mage_Admin_Model_Resource_User_Collection
    {
        if (null === $this->adminUserCollection) {
            /** @var Mage_Admin_Model_Resource_User_Collection adminUserCollection */
            $this->adminUserCollection = Mage::getSingleton('admin/user')->getCollection();
        }

        return $this->adminUserCollection;
    }
}
