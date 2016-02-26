<?php
class Learn_BulkImageUpload_Block_Adminhtml_Bulkimageupload extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_bulkimageupload';
        $this->_blockGroup = 'bulkimageupload';
        $this->_headerText = Mage::helper('bulkimageupload')->__('Bulk Product Images Upload');
        parent::__construct();
        $this->_addButton('add', array(
            'label'   => Mage::helper('bulkimageupload')->__('Upload New Images'),
            'onclick' => "setLocation('". $this->getUrl('*/*/bulkupload') ."')",
            'class'   => 'add'
        ));
        // $this->_addButtonLabel = Mage::helper('bulkimageupload')->__('Upload New Images');

    }
}