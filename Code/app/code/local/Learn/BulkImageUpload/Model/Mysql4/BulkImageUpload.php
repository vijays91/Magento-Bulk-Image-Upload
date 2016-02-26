<?php
class Learn_BulkImageUpload_Model_Mysql4_BulkImageUpload extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
		$this->_init('bulkimageupload/bulkimageupload', 'bpiu_id');
	}
}