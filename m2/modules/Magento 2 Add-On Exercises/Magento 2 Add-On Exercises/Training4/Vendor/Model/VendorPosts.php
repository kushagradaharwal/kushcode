<?php
namespace Training4\Vendor\Model;

class VendorPosts extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Training4\Vendor\Model\Resource\VendorPosts');
    }
}