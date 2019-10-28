<?php

/**
 * Change admin user edit form
 *
 * Class Smetana_Project_Block_Adminhtml_Permissions_User_Edit
 */
class Smetana_Project_Block_Adminhtml_Permissions_User_Edit extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main
{
    /**
     * Add fields to Admin user edit form
     *
     * @param void
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm(): Mage_Adminhtml_Block_Widget_Form
    {
        $parent = parent::_prepareForm();
        $permissionsUser = Mage::registry('permissions_user');

        if (
            !is_null($permissionsUser->getId())
            && $permissionsUser->getRole()->getData('role_name') == Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME
        ) {
            /** @var Mage_Adminhtml_Helper_Data $adminHelper */
            $adminHelper = Mage::helper('adminhtml');
            $form = $this->getForm();
            $fieldset = $form->addFieldset(
                'callcentre_fieldset',
                ['legend' => $adminHelper->__(Smetana_Project_Block_Options::SPECIALIST_ROLE_NAME)]
            );

            $fieldset->addField(
                'orders_type',
                'select',
                [
                    'name' => 'orders_type',
                    'label' => $adminHelper->__('Order Types'),
                    'id' => 'orders_type',
                    'title' => $adminHelper->__('Order Types'),
                    'class' => 'input-select',
                    'style' => 'width: 180px',
                    'componentDisabled' => true,
                    'options' => [
                        'non-selected' => $adminHelper->__('Не указан'),
                        'night' => $adminHelper->__('Nightly - (from 20.00 to 08.00)'),
                        'day' => $adminHelper->__('Daytime - (from 08.00 to 20.00)'),
                    ],
                ]
            );

            $fieldset->addField(
                'products_type',
                'select',
                [
                    'name' => 'products_type',
                    'label' => $adminHelper->__('Product Types'),
                    'id' => 'products_type',
                    'title' => $adminHelper->__('Product Types'),
                    'class' => 'input-select',
                    'style' => 'width: 180px',
                    'options' => Mage::getModel('smetana_project_model/attribute_source_products')->getAllOptions(),
                ]
            );

            /** @var Mage_Admin_Model_User $model */
            $model = Mage::registry('permissions_user');
            $data = $model->getData();
            unset($data['password']);
            $form->setValues($data);
            $this->setForm($form);
        }

        return $parent;
    }
}
