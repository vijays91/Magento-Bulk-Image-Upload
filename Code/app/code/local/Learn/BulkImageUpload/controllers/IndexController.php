<?php
class Learn_BulkImageUpload_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
	{
		$data = Mage::getModel('bulkimageupload/bulkimageupload')->getCollection()->getData();
		echo "<pre>";        
		print_r($data);	
		echo "</pre>";
	}
}
