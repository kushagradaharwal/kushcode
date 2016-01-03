<?php

namespace Training2\OrderController\Block;


class OrderController extends \Magento\Framework\View\Element\Template
{
 protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Order Controller'));
    }
  
}