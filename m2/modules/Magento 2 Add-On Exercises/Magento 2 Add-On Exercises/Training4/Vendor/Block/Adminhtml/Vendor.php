<?php
namespace Training4\Vendor\Block\Adminhtml;

class Vendor extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Training4_Vendor';
        $this->_headerText = __('Manage Vendor');
        $this->_addButtonLabel = __('Add New Vendor');
        parent::_construct();
    }
}