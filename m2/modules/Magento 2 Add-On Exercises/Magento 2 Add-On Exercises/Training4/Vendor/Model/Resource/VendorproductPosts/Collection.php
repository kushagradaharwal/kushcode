<?php
namespace Training4\Vendor\Model\Resource\VendorproductPosts;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Training4\Vendor\Model\VendorproductPosts', 'Training4\Vendor\Model\Resource\VendorproductPosts');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}