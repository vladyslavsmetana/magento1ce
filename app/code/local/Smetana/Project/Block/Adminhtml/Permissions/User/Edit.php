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
        $adminHelper = Mage::helper('adminhtml');
        $form = $this->getForm();
        $fieldset = $form->addFieldset(
            'callcentre_fieldset',
            ['legend' => $adminHelper->__('Специалист колл-центра')]
        );

        $roleField = $fieldset->addField(
            'callcentre_role',
            'select',
            [
                'name'      => 'callcentre_role',
                'label'     => $adminHelper->__('Роль в колл-центре'),
                'id'        => 'callcentre_role',
                'title'     => $adminHelper->__('Роль в колл-центре'),
                'class'     => 'input-select',
                'style'     => 'width: 180px',
                'options'   => [
                    'non-selected' => $adminHelper->__('Не указан'),
                    'specialist' => $adminHelper->__('Специалист колл-центра'),
                    'coordinator' => $adminHelper->__('Координатор колл-центра'),
                ],
            ]
        );

        $ordersTypeField = $fieldset->addField(
            'orders_type',
            'select',
            [
                'name'      => 'orders_type',
                'label'     => $adminHelper->__('Тип заказов'),
                'id'        => 'orders_type',
                'title'     => $adminHelper->__('Тип заказов'),
                'class'     => 'input-select',
                'style'     => 'width: 180px',
                'options'   => [
                    'non-selected' => $adminHelper->__('Не указан'),
                    'night' => $adminHelper->__('Ночные - (с 20.00 до 08.00)'),
                    'day' => $adminHelper->__('Дневные - (с 08.00 до 20.00)'),
                ],
            ]
        );

        $productsTypeField = $fieldset->addField(
            'products_type',
            'select',
            [
                'name'    => 'products_type',
                'label'   => $adminHelper->__('Тип товаров'),
                'id'      => 'products_type',
                'title'   => $adminHelper->__('Тип товаров'),
                'class'   => 'input-select',
                'style'   => 'width: 180px',
                'options' => Mage::getModel('smetana_project_model/attribute_source_products')->getAllOptions(),
            ]
        );

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($roleField->getHtmlId(), $roleField->getName())
            ->addFieldMap($ordersTypeField->getHtmlId(), $ordersTypeField->getName())
            ->addFieldMap($productsTypeField->getHtmlId(), $productsTypeField->getName())
            ->addFieldDependence(
                $ordersTypeField->getName(),
                $roleField->getName(),
                'specialist'
            )
            ->addFieldDependence(
                $productsTypeField->getName(),
                $roleField->getName(),
                'specialist'
            )
        );

        $model = Mage::registry('permissions_user');
        $data = $model->getData();
        unset($data['password']);
        $form->setValues($data);
        $this->setForm($form);

        return $parent;
    }
}
