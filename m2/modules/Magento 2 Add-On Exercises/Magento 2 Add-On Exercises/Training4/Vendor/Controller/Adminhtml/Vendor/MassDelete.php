<?php
namespace Training4\Vendor\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $blogpostIds = $this->getRequest()->getParam('vendorpost');
        if (!is_array($blogpostIds) || empty($blogpostIds)) {
            $this->messageManager->addError(__('Please select blog post(s).'));
        } else {
            try {
                foreach ($blogpostIds as $postId) {
				
					$itemCollection = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->getCollection();		
					$itemCollection->addFieldToFilter('vendor_id', ['eq' => $postId]);
					foreach($itemCollection as $itemData){
						$itemDelete = $this->_objectManager->create('Training4\Vendor\Model\VendorproductPosts')->load($itemData['entity_id']);
						$itemDelete->delete();
					}
				
				
                    $post = $this->_objectManager->get('Training4\Vendor\Model\VendorPosts')->load($postId);
					$post->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($blogpostIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('vendor/*/index');
    }
}
