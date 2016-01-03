<?php
namespace Training5\VendorApi\Block;

class VendorApi extends \Magento\Framework\View\Element\Template
{

 protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Vendor APi Call - Training5_VendorApi'));
    }
	
}
