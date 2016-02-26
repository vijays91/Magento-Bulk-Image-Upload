<?php
class Learn_BulkImageUpload_Block_Adminhtml_Bulkimageupload_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

 public function __construct()
    {
        parent::__construct();
        $this->setId('bulkimageuploadGrid');
        $this->setDefaultSort('bpiu_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bulkimageupload/bulkimageupload')->getCollection();
        $this->setCollection($collection);		
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('bpiu_id', array(
            'header'    => Mage::helper('bulkimageupload')->__('ID'),
            'align'     => 'right',
			'type'		=> 'number',
            'index'     => 'bpiu_id',
        ));
        $this->addColumn('total_images_uploaded', array(
            'header'    => Mage::helper('bulkimageupload')->__('Total Images Uploaded'),
			'width'     => '100px',
            'index' 	=> 'total_images_uploaded',
        ));
        $this->addColumn('total_matched_images', array(
            'header'    => Mage::helper('bulkimageupload')->__('Total Matched Images'),
            'align'     => 'left',
			'width'		=> '200px',
            'index'     => 'total_matched_images',
        ));
        $this->addColumn('total_unmatched_images', array(
            'header'    => Mage::helper('bulkimageupload')->__('Total Unmatched Images'),
            'align'     => 'left',
			'width'		=> '200px',
            'index'     => 'total_unmatched_images',
        ));
        $this->addColumn('admin_id', array(
            'header'    => Mage::helper('bulkimageupload')->__('Uploaded By'),
            'align'     => 'left',
			'width'		=> '200px',
            'index'     => 'admin_id',
			'filter' 	=> false,
			'sortable'  => false,
			'renderer'	=> 'Learn_BulkImageUpload_Block_Adminhtml_Renderer_Adminname'
        ));
        $this->addColumn('upload_at', array(
            'header'    => Mage::helper('bulkimageupload')->__('Upload At'),
            'index' 	=> 'upload_at',
			'filter' 	=> false,
            'type' 		=> 'datetime',
            'width' 	=> '150px',
        ));
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}