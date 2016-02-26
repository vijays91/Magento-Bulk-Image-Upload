<?php
class Learn_BulkImageUpload_Adminhtml_BulkimageuploadController extends Mage_Adminhtml_Controller_Action 
{
    protected function _initAction()
    {
		$this->loadLayout()->_setActiveMenu('catalog/bulkimageupload')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Bulk Product Images Upload'), Mage::helper('adminhtml')->__('Bulk Product Images Upload'));
		return $this;
    }  

    public function indexAction() 
	{
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('bulkimageupload/adminhtml_bulkimageupload'));
        $this->renderLayout();
    }
    /*
     * Upload bulk image
     */
    public function bulkuploadAction() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
		$path = BP . DS ; /*- Base Path -*/
		$filepath = "media". DS ."bulkupload". DS ."import". DS;
		$filepath = $path. $filepath;
        $helper = Mage::helper('bulkimageupload');        
        $separator = $helper->bpiu_separate();
        
        $mediaAttribute = array ( 'thumbnail', 'small_image', 'image' );
        if($helper->bpiu_enable() && $separator) {
            if(is_writable($filepath)) {
                $files = glob("$filepath*.{jpg,gif,png,jpeg}", GLOB_BRACE);
                
                if(count($files) > 0 ){
                    $data = array();
                    $data['total_images_uploaded'] = count($files);
                    $data['total_matched_images'] = 0;
                    $data['total_unmatched_images'] = 0;
                    $data['admin_id'] = $this->getAdimUserId();
                    
                    $match_count = 0;
                    $match_image = array();
                    $un_match_count = 0;
                    $un_match_image = array();
                    foreach($files as $key => $image) {
                        $image_name = basename(pathinfo($image, PATHINFO_FILENAME), pathinfo($image, PATHINFO_EXTENSION));
                        $sku_order = explode($separator, $image_name);
                        $productSKU = (string) trim($sku_order[0]);
                        $sort_order = 0;

                        $product = Mage::getModel('catalog/product');
                        $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($productSKU);                    
                        try {
                            if ($id) {
                                $product->load($id);
                                $label = $product->getName();
                                if(is_array($sku_order)) {
                                    $sort_order = (isset($sku_order[1]) && is_numeric($sku_order[1])) ? $sku_order[1] : 0;
                                    $label = (isset($sku_order[2])) ? $sku_order[2] : $label;
                                }                                
                                if($sort_order) {
                                    // $product->addImageToMediaGallery($image , null, false, false );
                                   $this->upload_image($image, $productSKU, $sort_order, array(), $label);
                                    
                                } else {
                                    // $product->addImageToMediaGallery($image , $mediaAttribute, false, false );
                                   $this->upload_image($image, $productSKU, $sort_order, $mediaAttribute, $label);
                                }
                                //$product->save();
                                $match_image[] = $image;
                                $match_count++;
                            } else {
                                $un_match_image[] = $image;
                                $un_match_count++;
                            }
                        } catch (Exception $e) {echo $e->getMessage();}
                    }
                    $data['total_matched_images'] = $match_count;
                    $data['total_unmatched_images'] = $un_match_count;
                    $data['upload_at'] = date('Y-m-d H:i:s');
                    
                    $model = Mage::getModel('bulkimageupload/bulkimageupload')->setData($data);
                    try {
                        $insertId = $model->save()->getId();
                    } catch (Exception $e){
                        echo $e->getMessage();   
                    }

                    /*- Copy the matched -*/
                    if(count($match_image) > 0) {
                        $this->create_directory("media/bulkupload/bpiu_id_". $insertId ."/matched/");
                        foreach($match_image as $file){
                            $file_to_go = str_replace("import\\", "bpiu_id_". $insertId ."\\matched\\",  $file);
                            copy($file, $file_to_go);
                        }
                    }
                    
                    /*- Copy the unmatched -*/
                    if(count($un_match_image) > 0) {
                        $this->create_directory("media/bulkupload/bpiu_id_". $insertId ."/unmatched/");
                        foreach($un_match_image as $file){
                            $file_to_go = str_replace("import\\", "bpiu_id_". $insertId ."\\unmatched\\",  $file);
                            copy($file, $file_to_go);
                        }
                    }
                    
                    /*- Remove the files -*/
                    $this->removeFiles($filepath);
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                    
                        Mage::helper('bulkimageupload')->__("Successfully imported the images.<br/>
                        Matched images are downlaoad in the <b> media/bulkupload/bpiu_id_". $insertId ."/matched/</b> folder. <br/>
                        
                        Unmatched images are downlaoad in the <b> media/bulkupload/bpiu_id_". $insertId ."/unmatched/</b> folder. <br/> <br/>
                        
                            Total images uploaded  : ". $data['total_images_uploaded'] ."<br />
                            Total matched images   : ". $data['total_matched_images'] ."<br />
                            Total unmatched images : ". $data['total_unmatched_images'] ."<br />                        
                        ")
                    );
                    $this->_redirect('*/*/');
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('bulkimageupload')->__('No images avilable to import folder')
                    );
                    $this->_redirect('*/*/');
                }
            }
            else {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('bulkimageupload')->__('Importing File are needs to be writable permissions')
                );
                $this->_redirect('*/*/');
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('bulkimageupload')->__('Please enable the extension <a href="'. Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/bulkimageupload_tab"). '">click here</a>')
            );
            $this->_redirect('*/*/');
        }
    }
    
    private function removeFiles($filepath) {
        $files = glob($filepath . '/*');
        foreach ($files as $file) {
            is_dir($file) ? removeFiles($file) : unlink($file);
        }
        return true;
    }
    
    protected function upload_image($image, $product_sku, $sort_order, $mediaAttribute = array(), $label) 
    {
        $media = Mage::getModel("catalog/product_attribute_media_api"); 
        $newImage = array(
            'file' => array(
                'content' => base64_encode($image),
                'mime'  => $this->mimeType($image),
                'name'  => basename($image),
            ),
            'label'     => $label,
            'position'  => $sort_order,
            'types'     => $mediaAttribute,
            'exclude'   => 0,
        );
        $media->create($product_sku, $newImage);
    }
    
    protected function getAdimUserId() {
        $user = Mage::getSingleton('admin/session'); 
        return $user->getUser()->getUserId();
    }
    
    /*
     * Image Type
     */    
    protected function mimeType($filename) {
        $pathInfo = pathinfo($filename, PATHINFO_EXTENSION);
        switch($pathInfo){
            case 'png':
                $mimeType = 'image/png';
                break;
            case 'jpg':
                $mimeType = 'image/jpeg';
                break;
            case 'jpeg':
                $mimeType = 'image/jpeg';
                break;
            case 'gif':
                $mimeType = 'image/gif';
                break;
        }
        return $mimeType;
    }
    
    /*- Storing Folder -*/
    protected function create_directory($saveDirectory) {
        /* $saveDirectory = "/media/bulkupload/bpiu_id_$insertId/"; */
        $baseDirectory = Mage::getBaseDir()."/";
        $saveDirectory = trim($saveDirectory, '/');
        $newDirectory = "";
        foreach(explode('/',$saveDirectory) as $val) {
            if(!is_dir($baseDirectory.$newDirectory.$val)){
                mkdir($baseDirectory.$newDirectory.$val, 0777);
                chmod($baseDirectory.$newDirectory.$val, 0777);
            }
            $newDirectory .= $val."/";
        }
        return true;
    }
    
    /*
     * Grid Filter
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('bulkimageupload/adminhtml_bulkimageupload_grid')->toHtml()
        );
    }
}