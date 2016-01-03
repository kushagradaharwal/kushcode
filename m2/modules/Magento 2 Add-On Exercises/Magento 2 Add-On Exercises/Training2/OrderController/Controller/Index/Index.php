<?php

namespace Training2\OrderController\Controller\Index;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Rss\UrlBuilderInterface;
use Magento\Framework\Url\Helper\Data;
class Index extends \Magento\Framework\App\Action\Action
{
	 
    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
		if ($this->getRequest()->getParam('json')) {
			$this->_view->loadLayout();
			$this->_view->renderLayout();
		}else{
			return $this->_redirect('orderinfo/index/index');
		}
    }
	
	
}