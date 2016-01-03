<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Training2\Specific404Page\Controller\Category;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Controller\Category\View
{
	protected function _initCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return $this->_redirect('category-not-found');
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
			 
        } catch (NoSuchEntityException $e) {
            return $this->_redirect('category-not-found');
        }
        if (!$this->_objectManager->get('Magento\Catalog\Helper\Category')->canShow($category)) {
          return $this->_redirect('category-not-found');
        }
        $this->_catalogSession->setLastVisitedCategoryId($category->getId());
        $this->_coreRegistry->register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_redirect('category-not-found');
        }

        return $category;
    }
	
}
