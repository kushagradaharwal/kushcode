<?php

namespace Training3\OrderInfo\Block;


class OrderInfo extends \Magento\Framework\View\Element\Template
{
 protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Order Info'));
    }
  
}