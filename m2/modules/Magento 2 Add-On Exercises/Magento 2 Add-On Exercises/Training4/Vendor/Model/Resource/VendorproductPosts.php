<?php
namespace Training4\Vendor\Model\Resource;

class VendorproductPosts extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('training4_vendor2product', 'entity_id');
    }
}