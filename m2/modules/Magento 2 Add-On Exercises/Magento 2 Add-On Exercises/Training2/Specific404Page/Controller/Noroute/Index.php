<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Training2\Specific404page\Controller\Noroute;

class Index extends \Magento\Cms\Controller\Noroute\Index
{
  
   
	
    public function execute()
    {  
        $pageId = $this->_objectManager->get(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_NO_ROUTE_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        /** @var \Magento\Cms\Helper\Page $pageHelper */
        $pageHelper = $this->_objectManager->get('Magento\Cms\Helper\Page');
        $resultPage = $pageHelper->prepareResultPage($this, $pageId);
        if ($resultPage) {
            return $this->_redirect('another-page');
        } else {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
           // $resultForward = $this->resultForwardFactory->create();
            //$resultForward->setController('index');
          //  $resultForward->forward('defaultNoRoute');
            return $this->_redirect('another-page');
        }
    }
}
