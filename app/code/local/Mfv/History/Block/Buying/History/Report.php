<?php
class Mfv_History_Block_Buying_History_Report extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
		
    }

	public function getOrderHistory()
	{
	 
		$_histrCollection =   Mage::registry("filtercollection");
		
		$curr_url =  Mage::helper("core/url")->getCurrentUrl();

		if($_histrCollection != null)
		{
          return $_histrCollection;
		}
		
		else if(strpos($curr_url, 'seasonfilter'))
        {
		  return $_histrCollection;
		}	
    	else {
				
				$model = Mage::getModel('history/history');
				$default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
				if (isset($default_customeraccount)) {
				$customerid = $default_customeraccount->getAccountNumber();
				}
				
				
				//echo 		$customerid ; //FAK00002
				$orders = $model->getOrders($customerid);

				//$ordersobject = new Varien_Object(array(
				//'orders' => $orders
				//));
				return $orders;
		}
	}
	
	
     public function getDefaultyear()
     {
        $dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
	    $sql = 'SELECT MMFRE3 AS "Season" 
				FROM OOHEAD INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
				WHERE OACONO = 1 AND OACUNO = "'.$cuno.'" AND (OAORST >=22 AND OAORST<=77)
				AND OAORTP LIKE ("'.'MF%%'.'")
				Group By MMFRE3 ORDER BY OAORNO ASC';
  
	    $collect = $dbh->query($sql);
		 
		$collecttion =  $collect->fetchAll();
		 
		return $collecttion; 
	 }
	
}