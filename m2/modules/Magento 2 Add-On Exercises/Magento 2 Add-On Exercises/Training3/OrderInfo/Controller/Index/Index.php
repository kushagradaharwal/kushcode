<?php

namespace Training3\OrderInfo\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
		$this->_view->loadLayout();
		$this->_view->renderLayout();
    }
	
	
}