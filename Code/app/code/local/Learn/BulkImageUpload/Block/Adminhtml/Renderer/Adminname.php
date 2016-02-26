<?php
class Learn_BulkImageUpload_Block_Adminhtml_Renderer_Adminname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
	{
        $admin_id = $row->getData('admin_id');
        $user_data = Mage::getModel('admin/user')->load($admin_id)->getData();
        return $user_data['firstname']." ".$user_data['lastname'];
	}
}