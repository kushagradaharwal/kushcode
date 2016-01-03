<?php
namespace Training4\Vendor\Model;

class VendorproductPosts extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
   public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
) {
    parent::__construct($context, $registry, $resource, $resourceCollection, $data);
}

 protected function _construct()
    {
        $this->_init('Training4\Vendor\Model\Resource\VendorproductPosts');
    }
}