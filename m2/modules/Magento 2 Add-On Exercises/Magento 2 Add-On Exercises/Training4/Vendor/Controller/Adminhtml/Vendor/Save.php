<?php
namespace Training4\Vendor\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
	  
	  
    public function __construct(
		Action\Context $context
		
        
    ){
	
		  parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		//vendor_posts  ,  vendor_id
        $data = $this->getRequest()->getPostValue();
				
				
				 
	//echo " SAVE"; 
	//echo "<pre>"; print_r($data); 
	//exit;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Training4\Vendor\Model\VendorPosts');

            $id = $this->getRequest()->getParam('vendor_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
				
				$itemCollection = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->getCollection();		
				$itemCollection->addFieldToFilter('vendor_id', ['eq' => $model->getId()]);
				foreach($itemCollection as $itemData){
					$itemDelete = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->load($itemData['entity_id']);
					$itemDelete->delete();
				}
				
				
				foreach($data['assign_products'] as $KK=>$VV){				
					$modelAAA = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts');				
					$modelAAA->setData(array('vendor_id'=>$model->getId(),'product_id'=>$VV));		
					$modelAAA->save();	
				}
				
				
				
                $this->messageManager->addSuccess(__('The vendor data  has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['vendor_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the vendor data'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['vendor_id' => $this->getRequest()->getParam('vendor_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
