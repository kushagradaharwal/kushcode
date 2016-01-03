<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Training2\Specific404page\Controller\Product;

class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * Redirect if product failed to load
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Forward
     */
    protected function noProductRedirect()
    {
        $store = $this->getRequest()->getQuery('store');
        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$this->getResponse()->isRedirect()) {
			return $this->_redirect('prouduct-not-found');
        }
    }

}
