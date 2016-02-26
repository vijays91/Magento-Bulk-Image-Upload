<?php
class Learn_BulkImageUpload_Helper_Data extends Mage_Core_Helper_Abstract 
{
    const XML_PATH_BPIU_ENABLE    = 'bulkimageupload_tab/bulkimageupload_setting/bulkimageupload_enable';
    const XML_PATH_BPIU_SEPARATE = 'bulkimageupload_tab/bulkimageupload_setting/bulkimageupload_separate';
    
    public function conf($code, $store = null) {
        return Mage::getStoreConfig($code, $store);
    }

	public function bpiu_enable($store) {
        return $this->conf(self::XML_PATH_BPIU_ENABLE, $store);
    }
    
	public function bpiu_separate($store) {
        return $this->conf(self::XML_PATH_BPIU_SEPARATE, $store);
    }    
}