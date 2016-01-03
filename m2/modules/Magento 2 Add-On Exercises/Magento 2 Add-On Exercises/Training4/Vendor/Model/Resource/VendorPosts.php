<?php
namespace Training4\Vendor\Model\Resource;

class VendorPosts extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('training4_vendor', 'vendor_id');
    }
}