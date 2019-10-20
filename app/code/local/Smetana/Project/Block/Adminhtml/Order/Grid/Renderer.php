<?php

/**
 * Initiator column renderer
 *
 * Class Smetana_Project_Block_Adminhtml_Order_Grid_Renderer
 */
class Smetana_Project_Block_Adminhtml_Order_Grid_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Change displayed column data
     *
     * @param Varien_Object $row
     *
     * @return null|string
     */
    public function render(Varien_Object $row)
    {
        $columnIndex = $this->getColumn()->getIndex();
        $columnData = $row->getData($columnIndex);
        return Mage::getModel('admin/user')
                ->load($columnData)
                ->getData('username') ?? $columnData;
    }
}
