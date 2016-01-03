<?php
namespace Training3\ExchangeRate\Block;

class ExchangeRate extends \Magento\Framework\View\Element\Template
{
 protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
	 
	public function getcurrencycodedata(){
		$store = $this->_getStore();
		return  $this->_storeManager->getStore($store)->getBaseCurrencyCode();		
	}
}
