<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Csproduct\Controller\Adminhtml\Group;

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Customer\Controller\Adminhtml\Group\Save
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;
	
	
	protected $_jsHelper;

	protected $_csgCollection;
	
	protected $_csgroup;
	
	protected $_fileUploaderFactory;
	
	protected $_fileSystem;
	
	protected $_productRepository;
	
	protected $_csModel;
	
	protected $_csgcModel;
	
	protected $_csgcCollection;
	
    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupDataFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupDataFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
		\Magento\Backend\Helper\Js $jsHelper,
		\Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgcollection,
		\Mconnect\Csproduct\Model\CsgroupproductFactory $csgroup,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploader, 
		\Magento\Framework\Filesystem $fileSystem,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Mconnect\Csproduct\Model\CsproductFactory $csModelProduct,
		\Mconnect\Csproduct\Model\CsgroupcategoryFactory $csgModelCategory,
		\Mconnect\Csproduct\Model\ResourceModel\Csgroupcategory\CollectionFactory $csgccollection,
		\Mconnect\Csproduct\Model\CsgroupwebsiteFactory $csGroupWebsiteModel,
		 array $data = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
		$this->_jsHelper = $jsHelper;
		$this->_csgCollection = $csgcollection;
		$this->_csgroup = $csgroup;		
		$this->_fileUploaderFactory = $fileUploader;
		$this->_fileSystem = $fileSystem;
		$this->_productRepository = $productRepository;
		$this->_csModel = $csModelProduct;
		$this->_csgcModel = $csgModelCategory;
		$this->_csgcCollection = $csgccollection;
		$this->_csGroupWebsiteModel = $csGroupWebsiteModel;
        parent::__construct(
            $context,
            $coreRegistry,
            $groupRepository,
            $groupDataFactory,
            $resultForwardFactory,
            $resultPageFactory,
			$dataObjectProcessor
			
        );
    }

    /**
     * Store Customer Group Data to session
     *
     * @param array $customerGroupData
     * @return void
     */
    protected function storeCustomerGroupDataToSession($customerGroupData)
    {
        if (array_key_exists('code', $customerGroupData)) {
            $customerGroupData['customer_group_code'] = $customerGroupData['code'];
            unset($customerGroupData['code']);
        }
        $this->_getSession()->setCustomerGroupData($customerGroupData);
    }
	
	
	public function loadByMultiple($groupId, $productId){
		$collection = $this->_csgCollection->create();
		$collection	->addFieldToFilter('product_id', $productId);
		$collection	->addFieldToFilter('group_id', $groupId); 
		return $collection->getFirstItem();
	}
	
    /**
     * Create or save customer group.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
		
		
		
		$id = $this->getRequest()->getParam('id');
		$data = $this->getRequest()->getPostValue();
		
		if($this->getRequest()->getParam('csgroup_product_set')){
		
			if($this->getRequest()->getParam('groupproduct_ids')){
				$data = $this->getRequest()->getPost();			
				$productIds = $this->_jsHelper->decodeGridSerializedInput($data['groupproduct_ids']);
				$csgCollection = $this->_csgCollection->create();
				$existcollection = $csgCollection->addFieldToFilter('group_id',$id);
				$existGroupProduct = array();
				foreach($existcollection as $child){            
					$existGroupProduct[] = $child->getProductId();            
				}
				
				if(!is_array($productIds) && $productIds == 1){
						$deleteProduct = $existGroupProduct;
				}else{
					$MergeArray = array_unique(array_merge($productIds, $existGroupProduct));
					$deleteProduct = array_diff($MergeArray,$productIds);
				}

				foreach($productIds as $productidsChild){
					$csgroupdata = $this->loadByMultiple($id, $productidsChild);

					if($csgroupdata->getData()){
						$modelId = $csgroupdata->getId();
						$csGroupModel = $this->_csgroup->create();	
						$csGroupModel->load($modelId);
						$csGroupModel->setGroupId($id);
						$csGroupModel->setProductId($productidsChild);	
						$csGroupModel->save();
						//$model->unsetData();
					}else{
						
						$csGroupModel = $this->_csgroup->create();			
						$csGroupModel->load(null);
						$csGroupModel->setProductId($productidsChild);
						$csGroupModel->setGroupId($id);
						$csGroupModel->save();
					}

				} 
				
				if($deleteProduct){
					foreach($deleteProduct as $deleteProductChild){
						$csgCollection = $this->_csgCollection->create();
						$csgCollection->addFieldToFilter('product_id',$deleteProductChild);
						$deleteCollection = $csgCollection->addFieldToFilter('group_id',$id);
							foreach($deleteCollection as $deleteData){
									$deleteData->delete();
							}					
							
					}
				}
				
							
				if(!empty($data['customer_group_product'])){
					
					$custompriceproductsBy = $data['customer_group_product'];
					$custompriceproductsBy = array_filter($custompriceproductsBy, function($value) { return $value !== ''; });			
					foreach($custompriceproductsBy as $productId => $price){						
						$csgroupdata = $this->loadByMultiple($id, $productId);					
						if($csgroupdata->getData()){						
							$modelId = $csgroupdata->getId();						
							$csGroupModel = $this->_csgroup->create();						
							$csGroupModel->load($modelId);						
							$csGroupModel->setCsgpPrice($price);							
							$csGroupModel->save();
							//$model->unsetData();
						}
								
					}
				}
					
				
			}else{
				
				$csgCollection = $this->_csgCollection->create();
				$deleteCollectionAll = $csgCollection->addFieldToFilter('group_id',$id);
				foreach($deleteCollectionAll as $deleteData){
							$deleteData->delete();
						}				
				
			}
		}
		
		
		/*------------------------------------------------------------------------*/
		
		$this->groupCategory();	
	
		
		/*----------------------csv group product upload -------------------------*/
		
		$this->csvGroupUpload();
		
		/*----------------------csv customer product upload -------------------------*/
		
		$this->csvProductUpload();	

		/*--------------------------------------------------------------------------*/	
		
		
		$taxClass = (int)$this->getRequest()->getParam('tax_class');

        /** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
        $customerGroup = null;
        if ($taxClass) {
            $id = $this->getRequest()->getParam('id');
            $resultRedirect = $this->resultRedirectFactory->create();
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');
                if ($id !== null) {
                    $customerGroup = $this->groupRepository->getById((int)$id);
                    $customerGroupCode = $customerGroupCode ?: $customerGroup->getCode();
                } else {
                    $customerGroup = $this->groupDataFactory->create();
                }
                $customerGroup->setCode(!empty($customerGroupCode) ? $customerGroupCode : null);
                $customerGroup->setTaxClassId($taxClass);

                $groupModel=$this->groupRepository->save($customerGroup);
				
					
				
				$this->saveWebsiteIdForGroup($groupModel->getId());

                $this->messageManager->addSuccess(__('You saved the customer group.'));
                $resultRedirect->setPath('customer/group');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                if ($customerGroup != null) {
                    $this->storeCustomerGroupDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $customerGroup,
                            '\Magento\Customer\Api\Data\GroupInterface'
                        )
                    );
                }
                $resultRedirect->setPath('customer/group/edit', ['id' => $id]);
            }
            return $resultRedirect;
        } else {
            return $this->resultForwardFactory->create()->forward('new');
        }
    }
	
	public function  saveWebsiteIdForGroup($groupId){

		$groupId=$groupId;		
	    $csgwebsite_id = $this->getRequest()->getParam('csgwebsite_id');
		
		$websiteColls=$this->_csGroupWebsiteModel->create()->getCollection()
							->addFieldTofilter('group_id',$groupId)
							->getFirstItem();	
		
		if($websiteColls->getData()){
			
		}else{
			
			$csWebsiteGroupModel = $this->_csGroupWebsiteModel->create();			
			$csWebsiteGroupModel->load(null);
			$csWebsiteGroupModel->setWebsiteId($csgwebsite_id);
			$csWebsiteGroupModel->setGroupId($groupId);
			$csWebsiteGroupModel->save();
			
		}
		
		return ;
	}
	
	public function  csvGroupUpload(){	
		
		$id = $this->getRequest()->getParam('id');
		$data = $this->getRequest()->getPostValue();
		
		if(isset($_FILES['csgroup_import_file']['name']) && $_FILES['csgroup_import_file']['name'] != '') {
			$imp_data = $_FILES['csgroup_import_file'];
			
			//-------------------------
			try {
				$uploader = $this->_fileUploaderFactory->create(['fileId' => 'csgroup_import_file']);
				$uploader->setAllowedExtensions(['csv']);
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$path = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)
				->getAbsolutePath('csproduct');
				$result = $uploader->save($path);
				
				$csvFile = $result['path'].$result['file'];
				$file_handle = fopen($csvFile, 'r');
				$field_head = fgetcsv($file_handle);
				
				$i = 0;
				while (!feof($file_handle) ) {
						$line_of_text = fgetcsv($file_handle);
						$filter_data = array();
						
						//print_r($line_of_text);
						if($line_of_text[0]!='' && $line_of_text[1]!='' && $id!='' ){
							
						$csvSku = $line_of_text[0];
						$_product=$this->_productRepository->get($csvSku);
						$csvProductId=$_product->getEntityId();	
							if(!empty($csvProductId)){
								
								$csvCustomPrice = $line_of_text[1];							
								$csvGroupId = $id;	
								
								$csgroupdata = $this->loadByMultiple($csvGroupId, $csvProductId);									
				
								if($csgroupdata->getData()){
									
									$modelId = $csgroupdata->getId();									
									$csGroupModel = $this->_csgroup->create();	
									
									$csGroupModel->load($modelId);
									$csGroupModel->setGroupId($csvGroupId);
									$csGroupModel->setProductId($csvProductId);	
									$csGroupModel->setCsgpPrice($csvCustomPrice);		
									$csGroupModel->save();
									//$model->unsetData();
								}else{						
									
									$csGroupModel = $this->_csgroup->create();			
									$csGroupModel->load(null);
									$csGroupModel->setGroupId($csvGroupId);
									$csGroupModel->setProductId($csvProductId);	
									$csGroupModel->setCsgpPrice($csvCustomPrice);		
									$csGroupModel->save();
								}
								
								
								
								
							}
							
							
						}
						
						
				}			
				
			}catch (Exception $e) {
			
			
			}
						
							
		}
		
		return ;
	}
	public function  csvProductUpload(){
		
		$id = $this->getRequest()->getParam('id');
		$data = $this->getRequest()->getPostValue();
		
		if(isset($_FILES['csproduct_import_file']['name']) && $_FILES['csproduct_import_file']['name'] != '') {
			$imp_data = $_FILES['csproduct_import_file'];
			
			//-------------------------
			try {
				$uploader = $this->_fileUploaderFactory->create(['fileId' => 'csproduct_import_file']);
				$uploader->setAllowedExtensions(['csv']);
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$path = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)
				->getAbsolutePath('csproduct');
				$result = $uploader->save($path);
				
				$csvFile = $result['path'].$result['file'];
				$file_handle = fopen($csvFile, 'r');
				$field_head = fgetcsv($file_handle);
				
								
				$i = 0;
				while (!feof($file_handle) ) {
					
					$line_of_text = fgetcsv($file_handle);
					$filter_data = array();
						
					if($line_of_text[0]!='' && $line_of_text[1]!='' && $line_of_text[2]!='' ){							
						$csvCustomerId = $line_of_text[0];
						$allsku = explode(";" , $line_of_text[1]);
					 	$followfromgroup = trim($line_of_text[2]);
						
						if(isset($line_of_text[3])){
						$csvCustomerPrice = $line_of_text[3];	
						}						
							
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($csvCustomerId);
						if(!empty($customerObj)){
							
							
							if($followfromgroup==1){
							$customerObj->setFollowproductsfromgroup('1');	
							}else{
							$customerObj->setFollowproductsfromgroup(0);	
							}
							
							$customerObj->save();
							
							foreach($allsku as $sku){
								
								$productObj=$this->_productRepository->get($sku);
								$productId=$productObj->getEntityId();

								$csdata = $this->csvCustomerProductExit($csvCustomerId, $productId);
								
								if($csdata->getData()){
									
									$modelId = $csdata->getId();									
									$csproductModel = $this->_csModel->create();									
									$csproductModel->load($modelId);
									$csproductModel->setCustomerId($csvCustomerId);
									$csproductModel->setProductId($productId);								
									$csproductModel->save();
									//$model->unsetData();
								}else{						
									
									$csproductModel = $this->_csModel->create();			
									$csproductModel->load(null);
									$csproductModel->setCustomerId($csvCustomerId);
									$csproductModel->setProductId($productId);	
									$csproductModel->save();
								}								
								
								
							}
							
							
							
						}
							
					}					
						
				}				
				
			}catch (Exception $e) {
			
			
			}
			
		}
		return ;
	}
	
	public function  csvCustomerProductExit($csvCustomerId, $productId){
		
		$csproductModel = $this->_csModel->create()->getCollection();
		$csproductModel	->addFieldToFilter('customer_id', $csvCustomerId); 
		$csproductModel	->addFieldToFilter('product_id', $productId);		
		return $csproductModel->getFirstItem();
		
	}
	
	
	public function  groupCategory(){
			
			$id = $this->getRequest()->getParam('id');
			$data = $this->getRequest()->getPostValue();
			$groupId = $this->getRequest()->getParam('id');
			
			if(!empty($data['group_cat_set'])){
			
				if(!empty($data['group_categories'])){
					
					$categoriesIdsArray = $data['group_categories'];
					$categoriesIdsList = implode(',' ,$categoriesIdsArray);
					
					
									
					$collectionGC=$this->_csgcCollection->create();
					$collectionGC->addFieldToFilter('group_id',$groupId);
					$csgcdata=$collectionGC->getFirstItem();				
									
					if($csgcdata->getData()){					
						$csgcModelId = $csgcdata->getId();					
						$csGroupCatModel = $this->_csgcModel->create();									
						$csGroupCatModel->load($csgcModelId);
						$csGroupCatModel->setCategoryIds($categoriesIdsList);								
						$csGroupCatModel->save();			
						
					
					}else{					
						$csGroupCatModel = $this->_csgcModel->create();	
						$csGroupCatModel->load(null);				
						$csGroupCatModel->setGroupId($groupId);		
						$csGroupCatModel->setCategoryIds($categoriesIdsList);								
						$csGroupCatModel->save();		
				
					}				
					
				}else{
					
					$collectionGC=$this->_csgcCollection->create();
					$deleteCollectionGC=$collectionGC->addFieldToFilter('group_id',$groupId);					
					foreach($deleteCollectionGC as $deleteDataGC){
								$deleteDataGC->delete();
					}					
					
				}
			}
			
			
	}
	
}
