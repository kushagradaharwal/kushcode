<?php
namespace Training4\Vendor\Controller\Adminhtml\Vendor;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('vendor_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
              
				$itemCollection = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->getCollection();		
				$itemCollection->addFieldToFilter('vendor_id', ['eq' => $id]);
				foreach($itemCollection as $itemData){
					$itemDelete = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->load($itemData['entity_id']);
					$itemDelete->delete();
				}
				

				// init model and delete
                $model = $this->_objectManager->create('Training4\Vendor\Model\VendorPosts');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
				
				
				
                // display success message
                $this->messageManager->addSuccess(__('The vendor data has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['vendor_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a vendor  post to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
