<?php
class Mfv_History_Block_Orderseason extends Mage_Core_Block_Template
{
	
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
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
				Group By MMFRE3 ORDER BY OAORNO DESC';
  
	    $collect = $dbh->query($sql);
		 
		$collecttion =  $collect->fetchAll();
		 
		return $collecttion; 
	}
	
	public function seasoncollectiondata($year)
	{
	    $dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
	    $sql = 'SELECT MMFRE3 AS "Season" , OBITNO AS "itemNumber", OBITDS AS "prodDesc", sum(OBORQT) AS "qtyOrdered" 
		FROM OOHEAD
		INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
		INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		WHERE  MMFRE3 = "'.$year.'" AND OACONO = 1 AND OACUNO = "'.$cuno.'"  AND (OAORST >=22 AND OAORST<=77)
		AND OAORTP LIKE ("'.'MF%%'.'") group by OBITNO
		ORDER BY OAORNO DESC ';
  
	    $collect = $dbh->query($sql);
		 
		$collecttion =  $collect->fetchAll();
		 
		return $collecttion; 
	 
	 
	}
    

	
	
	public function seasoncollectionqty($year)
	{
	 $dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
	    $sql = 'SELECT MMFRE3 AS "Season" , sum(OBNEPR) AS "priceEach" , sum(OBORQT) AS "qtyOrdered" 
		FROM OOHEAD
		INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
		INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		WHERE  MMFRE3 = "'.$year.'" AND OACONO = 1 AND OACUNO = "'.$cuno.'"  AND (OAORST >=22 AND OAORST<=77)
		AND OAORTP LIKE ("'.'MF%%'.'") group by MMFRE3
		ORDER BY OAORNO DESC ';
  
	    $collect = $dbh->query($sql);
		 
		$collecttion =  $collect->fetchAll();
		 
		return $collecttion; 
	 
	 
	}
    
	
}