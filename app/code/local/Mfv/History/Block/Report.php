<?php
class Mfv_History_Block_Report extends Mage_Core_Block_Template
{
	
    
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	public function getOrderHistory()
    {
	  $_Collection =   Mage::registry("filtercollection");
      return 	$_Collection;
    }
    
}