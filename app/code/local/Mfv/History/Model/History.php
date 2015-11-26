<?php

class Mfv_History_Model_History extends Mage_Core_Model_Abstract
{
	public function getOrderLines($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();

        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();

        $orderlines = new Varien_Data_Collection;

		$sql =' select * from vw_get_order_discounts where OBORNO = :id AND OBCUNO = :cuno ';

		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid, ':cuno'=>$cuno));
		
		while($line = $sth->fetch(PDO::FETCH_ASSOC)){
            Mage::log('view line:'.print_r($line, true), null, 'LeanSwift.log');
        	$orderlines->addItem(new Varien_Object($line));
      	}
      	return $orderlines;
	}

	public function getItemShippingMethod($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orderlines = new Varien_Data_Collection;
		/* $sql = 'SELECT
		OBITNO AS "sku",
		OBMODL AS "shippingmethod"
		FROM OOLINE
		WHERE OBORNO = :id
		AND OBCONO = 1'; */
		$sql = 'SELECT CSYTAB.CTTX15 AS "shippingmethod" , OOLINE.OBITNO  AS "sku"
				FROM CSYTAB 
				INNER JOIN OOLINE ON CSYTAB.CTSTKY = OOLINE.OBMODL 
				WHERE CSYTAB.CTSTCO=\'MODL\' AND CSYTAB.CTCONO=1 AND OOLINE.OBORNO= :id AND OOLINE.OBCONO = 1 ';
		
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid));
		while($line = $sth->fetch(PDO::FETCH_ASSOC)){
        	$orderlines->addItem(new Varien_Object($line));
      	}
      	return $orderlines;
	}

	public function getItemShippingCost($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orderlines = new Varien_Data_Collection;
		$sql = 'SELECT
		O7ORNO AS "orderid",
		O7PONR AS "row",
		O7CRAM AS "amount"
		FROM OOLICH
		WHERE O7ORNO = :id
		AND O7CONO = 1
		AND O7CRID = "FRT-ES"';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid));
		while($line = $sth->fetch(PDO::FETCH_ASSOC)){
        	$orderlines->addItem(new Varien_Object($line));
      	}
      	return $orderlines;
	}

	public function getOrderInfo($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();

		/* $sql = 'SELECT
		OOHEAD.OATEPY AS "paymentterms",
		OOHEAD.OAORDT AS "date",
		OOHEAD.OACUOR AS "ponumber",
		OOHEAD.OAYREF AS "placedby",
		OOHEAD.OAOBLC AS "onhold",
		OCUSAD.OPCUA1 AS "address1",
		OCUSAD.OPCUA2 AS "address2",
		OCUSAD.OPCUA3 AS "address3",
		OCUSAD.OPCUA4 AS "address4",
		OOHEAD.OAORNO AS "confirmation",
		OOHEAD.OATEDL AS "deliveryterms",
		OCUSMA.OKCUNM AS "company",
		OOHEAD.OAORST AS "status",
		OCUSAD.OPCUNM AS "shipto"
		 FROM OOHEAD
                INNER JOIN OCUSMA ON OOHEAD.OACUNO = OCUSMA.OKCUNO
                INNER JOIN OCUSAD ON OOHEAD.OAADID = OCUSAD.OPADID AND OOHEAD.OACONO= OCUSAD.OPCONO
                WHERE OCUSAD.OPCUNO = OOHEAD.OACUNO  AND OCUSAD.OPCONO="1"  AND OOHEAD.OAORNO = :id
                LIMIT 1'; */
		$sql = 'SELECT
		OOHEAD.OATEPY AS "paymentterms",
		OOHEAD.OAORDT AS "date",
		OOHEAD.OACUOR AS "ponumber",
		OOHEAD.OAYREF AS "placedby",
		OOHEAD.OAOBLC AS "onhold",
		OCUSAD.OPCUA1 AS "address1",
		OCUSAD.OPCUA2 AS "address2",
		OCUSAD.OPCUA3 AS "address3",
		OCUSAD.OPCUA4 AS "address4",
		OOHEAD.OAORNO AS "confirmation",
		CSYTAB.CTTX15  AS "deliveryterms",
		OCUSMA.OKCUNM AS "company",
		OOHEAD.OAORST AS "status",
		OCUSAD.OPCUNM AS "shipto"
		 FROM OOHEAD
                INNER JOIN OCUSMA ON OOHEAD.OACUNO = OCUSMA.OKCUNO
                INNER JOIN OCUSAD ON OOHEAD.OAADID = OCUSAD.OPADID AND OOHEAD.OACONO= OCUSAD.OPCONO
				INNER JOIN CSYTAB ON CSYTAB.CTSTKY  = OOHEAD.OATEDL
                WHERE OCUSAD.OPCUNO = OOHEAD.OACUNO  AND OCUSAD.OPCONO="1"  AND OOHEAD.OAORNO = :id
                AND OOHEAD.OACUNO= :cuno
				AND CSYTAB.CTSTCO=\'TEDL\'
				AND CSYTAB.CTCONO = 1
                LIMIT 1';


        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid, ':cuno'=>$cuno));
		$orderdata = $sth->fetch(PDO::FETCH_ASSOC);
		
		// new sql to get Payment Term base Payment Code
		$sql = ' SELECT CSYTAB.CTTX15 AS "paymentterms"
				 FROM CSYTAB WHERE CSYTAB.CTSTCO=\'TEPY\'
				 AND CSYTAB.CTCONO = 1
				 AND CSYTAB.CTSTKY = :paymentCode
		LIMIT 1';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':paymentCode'=>$orderdata['paymentterms']));
		$orderPaymentData = $sth->fetch(PDO::FETCH_ASSOC);
		$orderdata['paymentterms'] = $orderPaymentData['paymentterms'];
		// end new update to get payment term
		
		return $orderdata;
	}
   
  
	public function getOrders($customerid = null)
	{
	  $dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
		if($_GET['dir'] != null && $_GET['order'] != null )
		{
		 	$sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
		 ORDER BY '.$_GET['order'].' '.$_GET['dir'].' ';
		}
		else
		{
		$sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC' ;
         } 
		// echo $sql;die;
		 $collect = $dbh->query($sql);
		 
		 $collecttion =  $collect->fetchAll();
		 
		 return $collecttion; 
		 
//$sql = 'SELECT
	//	OAORDT AS "date",
		//OACUOR AS "ponumber",
	//	OAOBLC AS "onhold",
		//OAORST AS "status",	
		//OAORNO AS "ordernumber",
		//OACUNO AS  "accountnumber"
		//FROM OOHEAD
		//WHERE OACUNO = :customerid AND (OAORST >=22 AND OAORST<=77)
		//ORDER BY OAORDT DESC';

		//die;
//$sth->bindParam(':customerid', $customerid, PDO::PARAM_STR, 12);
		
		
	
      	
		//echo "<pre>";
        //print_r($orders);
		//die;
		
		
		
		
	}
	
	public function getcolumnFilter($dir,$order,$orderdval,$yearval)
	 {

		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
 		//$cuno = "FAZ02348A";
if($dir && !$orderdval && !$yearval)
{ 	   
 	   $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.' ';
		
}
else if($dir && $orderdval && !$yearval)
{
	$sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE OAORST="'.$orderdval.'" OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.'';
}
else if($dir && !$orderdval && $yearval)
{		
    
	$sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3="'.$yearval.'"   AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.' ';
			
}

else if($dir && $orderdval && $yearval)
{		
    
	  $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3="'.$yearval.'"  AND OAORST="'.$orderdval.'"  AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.' ';
			
}
		
	     $collect = $dbh->query($sql);
		 
		 $collecttion =  $collect->fetchAll();
	    
		 return $collecttion; 
    }
	
	
	
	
	 public function getFilter($val)
	 {
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
        $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  OAORST = "'.$val.'"  AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
		 $collect = $dbh->query($sql);
		 
		 $collecttion =  $collect->fetchAll();
		 
		 return $collecttion; 
    }
	
	
	public function getseasonFilter($orderseason ,$yearseason,$valdirval ,$ordercolumnval)
	 {
	   //$orderseason;
		//$yearseason."year";
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
		
		if($yearseason && !$orderseason)
		{
	        if($valdirval != null && $ordercolumnval!= null)
			{ 
			
			$sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3 = "'.$yearseason.'"  AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
	    	ORDER BY '.$ordercolumnval.' '.$valdirval.'';
			}
			else
			{
			 $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3 = "'.$yearseason.'"  AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
			}
			
		
        }
	   else if($orderseason && !$yearseason)
		   {
		   
		    if($valdirval != null && $ordercolumnval!= null)
			{
			   if($orderseason == "22-66")
			   {
			 $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
						OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
						OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
						OBALUN AS "Doses" , OBFDED AS "deliveryDate"
						FROM OOHEAD
								  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
							INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
						WHERE  OAORST BETWEEN 22 AND 66 AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
						AND OAORTP LIKE ("'.'MF%%'.'")
					   ORDER BY '.$ordercolumnval.' '.$valdirval.'';
				}
                   else				
					{			 
			     $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
						OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
						OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
						OBALUN AS "Doses" , OBFDED AS "deliveryDate"
						FROM OOHEAD
								  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
							INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
						WHERE  OAORST = "'.$orderseason .'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
						AND OAORTP LIKE ("'.'MF%%'.'")
						ORDER BY '.$ordercolumnval.' '.$valdirval.'';
		 
		         }
				}
               else
               {
			   
			    if($orderseason == "22-66")
			   {
			     $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
						OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
						OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
						OBALUN AS "Doses" , OBFDED AS "deliveryDate"
						FROM OOHEAD
								  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
							INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
						WHERE  OAORST BETWEEN 22 AND 66 AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
						AND OAORTP LIKE ("'.'MF%%'.'")
						ORDER BY OAORNO DESC';
				}
                   else				
					{			 
			   $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
						OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
						OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
						OBALUN AS "Doses" , OBFDED AS "deliveryDate"
						FROM OOHEAD
								  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
							INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
						WHERE  OAORST = "'.$orderseason .'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
						AND OAORTP LIKE ("'.'MF%%'.'")
						ORDER BY OAORNO DESC';
		
		         }
			   
			    }			   
		   }	   
       else if($yearseason && $orderseason)
			{
			
			 if($valdirval != null && $ordercolumnval!= null)
			 {
		    $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3 = "'.$yearseason.'" AND  OAORST = "'.$orderseason.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
		    ORDER BY '.$ordercolumnval.' '.$valdirval.'';
	         }
			 else
			 {
			   $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3 = "'.$yearseason.'" AND  OAORST = "'.$orderseason.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
			 }
			}
	  else if($valdirval && $ordercolumnval && !$yearseason || !$orderseason)
            {
		   $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  OACONO = 1  AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
		    ORDER BY '.$ordercolumnval.' '.$valdirval.'';
			  
			}	  
		
		 if(isset($valdirval) || isset($ordercolumnval) || isset($yearseason) || isset($orderseason))
		 {
			 $collect = $dbh->query($sql);
			 
			 $collecttion =  $collect->fetchAll();
			 
			 return $collecttion;
         }
         else
		 {
		   $collecttion =  $this->getOrders();
		   return $collecttion;
		   //$url = Mage::getBaseUrl()."history/index/seasonfilter";
		   //Mage::app()->getResponse()->setRedirect($url);
		  
		 }
		die; 
    }
	
    public function getordertotalbyseason()
	 {
	 
	  $dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
        $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
	    $sql = 'SELECT MMFRE3 AS "Season" 
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE   OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
  
		$collect = $dbh->query($sql);
		 
		 $collecttion =  $collect->fetchAll();
		 
		 return $collecttion; 
	 }
	public function getQuickAddtoCardOrder($customerid = null, $page, $limit){
		$start = (($page-1)*$limit);
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$orders = new Varien_Data_Collection;
		
		$limit = intval($limit);
		
		$sql = 'SELECT
		OAORDT AS "date",
		OACUOR AS "ponumber",
		OAOBLC AS "onhold",
		OAORST AS "status",
		OAORNO AS "ordernumber"
		FROM OOHEAD
		WHERE OACUNO = :customerid AND (OAORST >=22 AND OAORST<=77)
		AND OAORTP IN (\'WEB\', \'130\', \'100\')
		ORDER BY OAORDT DESC
		LIMIT :start,:rows';
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':start', $start, PDO::PARAM_INT);
		$sth->bindParam(':rows', $limit, PDO::PARAM_INT);
		$sth->bindParam(':customerid', $customerid, PDO::PARAM_STR, 12);
		$sth->execute();
		while($order = $sth->fetch(PDO::FETCH_ASSOC)){
			$orders->addItem(new Varien_Object($order));
		}
		return $orders;
	}
	public function getOrderCount($customerid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();

		$sql = 'SELECT
		COUNT(*) AS "numRows"
		FROM OOHEAD
		WHERE OACUNO = :customerid
		LIMIT 1';
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':customerid', $customerid, PDO::PARAM_STR, 12);
		$sth->execute();
		$count = $sth->fetch(PDO::FETCH_ASSOC);

      	return $count;
	}

	public function getInvoices($customerid = null, $page, $limit)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$invoices = new Varien_Data_Collection;
		$start = (($page-1)*$limit);
		$limit = intval($limit);

		/* JOINS SOLUTION TAKES 25 MINUTES TO QUERY */
		// $sql = 'SELECT
		// sum(if(FSLEDG.ESTRCD=10,FSLEDG.ESCUAM,0)) as "amount",
		// sum(if(FSLEDG.ESTRCD=10,FSLEDG.ESCUAM,0)+if(FSLEDG.ESTRCD=20,FSLEDG.ESCUAM,0)) AS "outstanding",
		// FSLEDG.ESTRCD AS "status",
		// FSLEDG.ESCUNO AS "customernumber",
		// FSLEDG.ESCINO AS "invoicenumber",
		// FSLEDG.ESIVDT AS "invoicedate",
		// FSLEDG.ESDUDT AS "duedate",
		// OINVOL.ONIVNO,
		// OINVOL.ONORNO,
		// OOHEAD.OAORNO,
		// OOHEAD.OAOREF,
		// OOHEAD.OACUOR
		// FROM FSLEDG
		// LEFT JOIN OINVOL ON FSLEDG.ESCINO = OINVOL.ONIVNO
		// LEFT JOIN OOHEAD ON OINVOL.ONORNO = OOHEAD.OAORNO
		// WHERE FSLEDG.ESCUAM <> 0
		// AND FSLEDG.ESARCD = 0
		// AND FSLEDG.ESCUNO = :id
		// GROUP BY FSLEDG.ESCINO, FSLEDG.ESCUNO';

		$sql = 'SELECT
		sum(if(ESTRCD=10,ESCUAM,0)) as "amount",
		sum(if(ESTRCD=10,ESCUAM,0)+if(ESTRCD=20,ESCUAM,0)) AS "outstanding",
		ESTRCD AS "status",
		ESCUNO AS "customernumber",
		ESCINO AS "invoicenumber",
		max(ESIVDT) AS "invoicedate",
		max(ESDUDT) AS "duedate"
		FROM FSLEDG
		WHERE ESCUAM <> 0
		AND ESARCD = 0
		AND ESCUNO = :id 
		GROUP BY ESCINO, ESCUNO
		HAVING sum(if(FSLEDG.ESTRCD=10,FSLEDG.ESCUAM,0)+if(FSLEDG.ESTRCD=20,FSLEDG.ESCUAM,0)) <> 0
		ORDER BY ESIVDT DESC
		LIMIT :start,:rows';

		$sth = $dbh->prepare($sql);
		$sth->bindParam(':start', $start, PDO::PARAM_INT);
		$sth->bindParam(':rows', $limit, PDO::PARAM_INT);
		$sth->bindParam(':id', $customerid, PDO::PARAM_STR, 12);
		$sth->execute();
		
		$tempInvoices = array();
		$invoiceNumbers = array();
		
		while($invoice = $sth->fetch(PDO::FETCH_ASSOC)){
			$invoiceNumbers[] = (int)$invoice['invoicenumber'];
			$tempInvoices[$invoice['invoicenumber']] = $invoice;
      	}
      	
      	//Load customer PO number
      	$poNumbers = $this->getCustomerPOS($customerid, $invoiceNumbers);
      	foreach ($tempInvoices as $invoiceNumber => $invoice) {
      		$invoiceNumber = intval($invoiceNumber);
      		$invoice['ponumber'] = $poNumbers[$invoiceNumber];
      		$invoices->addItem( new Varien_Object($invoice) );
      	}
      	return $invoices;
	}
	
	public function getCustomerPOS($customerid, $invoiceNumbers) {
		$dbh = Mage::helper('m3connection')->getM3Connection();
		//Load customer PO number
		$sqlPo = 'SELECT
      	OOHEAD.OAORNO AS "orderid",
      	OINVOL.ONIVNO AS "invoicenumber",
		OOHEAD.OACUOR AS "ponumber"
		FROM OOHEAD
      	INNER JOIN OINVOL ON OOHEAD.OAORNO = OINVOL.ONORNO
		WHERE OOHEAD.OACUNO =\''.$customerid.'\' AND OOHEAD.OACONO = 1 AND OINVOL.ONIVNO IN (\''.implode('\',\'', $invoiceNumbers).'\')';
		 
		$sth = $dbh->prepare($sqlPo);
		$sth->execute();
		$poNumbers = array();
		while( $item = $sth->fetch(PDO::FETCH_ASSOC) ) {
			$invoiceNumber = $item['invoicenumber'];
			$poNumbers[$invoiceNumber] = $item['ponumber'];
		}
		return $poNumbers;
	}

	public function getInvoiceCount($customerid = null)
	{
		// $dbh = Mage::helper('m3connection')->getM3Connection();
		// $sql = 'SELECT
		// ESCINO,
		// ESCUNO,
		// COUNT(DISTINCT ESCINO) AS "numRows"
		// FROM FSLEDG
		// WHERE ESCUNO = :customerid
		// AND ESCUAM <> 0
		// AND ESARCD = 0
		// HAVING sum(if(ESTRCD=10,ESCUAM,0)+if(ESTRCD=20,ESCUAM,0)) <> 0
		// LIMIT 1';

		// $sth = $dbh->prepare($sql);
		// $sth->bindParam(':customerid', $customerid, PDO::PARAM_STR, 12);
		// $sth->execute();
		// $count = $sth->fetch(PDO::FETCH_ASSOC);

  //     	return $count;

      	$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$select = $connection ->select()
		->from(array('invoiceTbl' => 'FSLEDG'),
		array('invoiceTbl.ESTRCD','invoiceTbl.ESCUNO','invoiceTbl.ESCUAM'))
		->where('invoiceTbl.ESCUAM <> 0 ' )
		->where('invoiceTbl.ESCUNO = ?', $customerid )
		->group( array('invoiceTbl.ESCINO'))
		->having('sum( if( invoiceTbl.ESTRCD=10, invoiceTbl.ESCUAM,0) + if( invoiceTbl.ESTRCD=20,invoiceTbl.ESCUAM,0)) <> 0');

		$sql = 'select COUNT(*) AS numRows FROM (' .$select->__toString() .')query';	// query is the name of the FROM every from need a name.

		$dbh = Mage::helper('m3connection')->getM3Connection();
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$count = $sth->fetchColumn();

		return $count;
	}

	public function getInvoiceLines($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$invoicelines = new Varien_Data_Collection;

		$sql = 'SELECT
		ONORNO
		FROM OINVOL
		WHERE ONIVNO = :id
		AND ONCONO = 1
		LIMIT 1';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid));
		while($line = $sth->fetch(PDO::FETCH_ASSOC)){
			return $line['ONORNO'];
      	}
	}
	public function getInvoiceInfo($orderid = null, $invoiceid = null){
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$sql = 'select confirmationnumber as confirmation, paymentterms, invoicedate, invoicedate as date, ponumber, placedby, saddress1 as address1, saddress2 as address2, scity as address3, sstate as address4, shipto, deliveryterms, company, customeraccount as customernumber, duedate, invoiceamount as amount, outstandingbalance as outstanding from vw_get_invoice_details where invoicenumber = :id';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$invoiceid));
		$orderdata = $sth->fetch(PDO::FETCH_ASSOC);
		return $orderdata;
	}
	
	public function getInvoiceInfoold($orderid = null, $invoiceid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();

		// OSBSTD Byta till denna tabell.

		/* $sql = 'SELECT
		OOHEAD.OATEPY AS "paymentterms",
		OOHEAD.OAORDT AS "date",
		OOHEAD.OACUOR AS "ponumber",
		OOHEAD.OAYREF AS "placedby",
		OOHEAD.OAOBLC AS "onhold",
		OCUSAD.OPCUA1 AS "address1",
		OCUSAD.OPCUA2 AS "address2",
		OCUSAD.OPCUA3 AS "address3",
		OCUSAD.OPCUA4 AS "address4",
		OCUSAD.OPCUNM AS "shipto",
		OOHEAD.OAORNO AS "confirmation",
		OOHEAD.OATEDL AS "deliveryterms",
		OCUSMA.OKCUNM AS "company"
		FROM OOHEAD
		INNER JOIN OCUSMA ON OOHEAD.OACUNO = OCUSMA.OKCUNO
		INNER JOIN OCUSAD ON OOHEAD.OAADID = OCUSAD.OPADID
		WHERE OOHEAD.OAORNO = :id				
		LIMIT 1'; */
		$sql = 'SELECT
		OOHEAD.OATEPY AS "paymentterms",
		OOHEAD.OAORDT AS "date",
		OOHEAD.OACUOR AS "ponumber",
		OOHEAD.OAYREF AS "placedby",
		
				//OOHEAD.OAOBLC AS "onhold",
		
		OCUSAD.OPCUA1 AS "address1",
		OCUSAD.OPCUA2 AS "address2",
		OCUSAD.OPCUA3 AS "address3",
		OCUSAD.OPCUA4 AS "address4",
		OCUSAD.OPCUNM AS "shipto",
		OOHEAD.OAORNO AS "confirmation",
		CSYTAB.CTTX15  AS "deliveryterms",
		OCUSMA.OKCUNM AS "company"
		FROM OOHEAD
		INNER JOIN OCUSMA ON OOHEAD.OACUNO = OCUSMA.OKCUNO
		INNER JOIN OCUSAD ON OOHEAD.OAADID = OCUSAD.OPADID AND OOHEAD.OACONO= OCUSAD.OPCONO
		INNER JOIN CSYTAB ON CSYTAB.CTSTKY  = OOHEAD.OATEDL
		WHERE OOHEAD.OAORNO = :id AND OCUSAD.OPCUNO = OOHEAD.OACUNO  AND OCUSAD.OPCONO="1"
		AND CSYTAB.CTSTCO=\'TEDL\'
		AND CSYTAB.CTCONO = 1
		LIMIT 1';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid));
		$orderdata = $sth->fetch(PDO::FETCH_ASSOC);
		// new sql to get Payment Term base Payment Code
		$sql = ' SELECT CSYTAB.CTTX15 AS "paymentterms"
				 FROM CSYTAB WHERE CSYTAB.CTSTCO=\'TEPY\' 
				 AND CSYTAB.CTCONO = 1 
				 AND CSYTAB.CTSTKY = :paymentCode
		LIMIT 1';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':paymentCode'=>$orderdata['paymentterms']));
		$orderPaymentData = $sth->fetch(PDO::FETCH_ASSOC);
		$orderdata['paymentterms'] = $orderPaymentData['paymentterms'];
		
		// end new update to get payment term
		$sql = 'SELECT
		sum(if(ESTRCD=10,ESCUAM,0)) as "amount",
		sum(if(ESTRCD=10,ESCUAM,0)+if(ESTRCD=20,ESCUAM,0)) AS "outstanding",
		ESTRCD AS "status",
		ESCUNO AS "customernumber",
		ESCINO AS "invoicenumber",
		ESDUDT AS "duedate",
		ESIVDT as "invoicedate"
		FROM FSLEDG
		WHERE ESCUAM <> 0
		AND ESARCD = 0
		AND ESCINO = :id
		GROUP BY ESCINO, ESCUNO';

		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$invoiceid));
		$invoicedata = $sth->fetch(PDO::FETCH_ASSOC);

		$orderdata['amount'] = $invoicedata['amount'];
		$orderdata['status'] = $invoicedata['status'];
		$orderdata['ordernumber'] = $orderid;
		$orderdata['customernumber'] = $invoicedata['customernumber'];
		$orderdata['outstanding'] = $invoicedata['outstanding'];
		$orderdata['duedate'] = $invoicedata['duedate'];
		$orderdata['invoicedate'] = $invoicedata['invoicedate'];

		return $orderdata;
	}

	public function getInvoiceLinesDetails($orderid = null)
	{
		$dbh = Mage::helper('m3connection')->getM3Connection();
		$invoicelines = new Varien_Data_Collection;
		// $sql = 'SELECT
		// UCSAPR AS "contractprice",
		// UCNEPR AS "discountprice",
		// UCMODL AS "shippingmethod",
		// UCITDS AS "itemname"
		$sql = 'SELECT
		UCORQT AS "itemquantity",
		UCSPUN AS "unitmeasure",
		UCDIA1 AS "fffdiscount1",
		UCDIA2 AS "fffdiscount2",
		UCDIA3 AS "fffdiscount3",
		UCDIA4 AS "fffdiscount4",
		UCDIA5 AS "fffdiscount5",
		UCDIA6 AS "fffdiscount6",
		UCITNO AS "sku"
		FROM OSBSTD
		WHERE UCORNO = :id
		AND UCCONO = 1';
		$sth = $dbh->prepare($sql);
		$sth->execute(array(':id'=>$orderid));
		while($line = $sth->fetch(PDO::FETCH_ASSOC)){
        	$invoicelines->addItem(new Varien_Object($line));
      	}
      	return $invoicelines;
	}

	public function getDiscountLabels($program, $trans , $inputData, $mode)
    {
        //cono = 001, FACI = FFF
        $soap_server_url = Mage::helper('m3connection')->getSoapUrl();
        $soap_client = new SoapClient(null, array(
            'location' => $soap_server_url,
            'uri'      => $program,
            'trace'    => 1,
            'use' => SOAP_LITERAL,
            'style' => SOAP_DOCUMENT,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP
        ));
        $data_string = ""; // xml for parameters
        foreach($inputData as $key=>$value) {
            $data_string .= "<".$key.">".$value."</".$key.">";
        }

        $params = new SoapVar('<tns:'.$trans.' xmlns:tns="'.$program.'">'.$data_string.'</tns:'.$trans.'>', XSD_ANYXML);
        $soap_response = $soap_client->$trans($params);
        return $soap_response;
    }
    public function getFullOrders($customerid = null)
    {
    	$dbh = Mage::helper('m3connection')->getM3Connection();
    	$orders = new Varien_Data_Collection;
    
    	$sql = 'SELECT
		OAORDT AS "date",
		OACUOR AS "ponumber",
		OAOBLC AS "onhold",
		OAORST AS "status",
		OAORNO AS "ordernumber"
		FROM OOHEAD
		WHERE OACUNO = :customerid AND (OAORST >=22 AND OAORST<=77)
		ORDER BY OAORDT DESC';
    	$sth = $dbh->prepare($sql);
    	$sth->bindParam(':customerid', $customerid, PDO::PARAM_STR, 12);
    	$sth->execute();
    	while($order = $sth->fetch(PDO::FETCH_ASSOC)){
    		$orders->addItem(new Varien_Object($order));
    	}
    	return $orders;
    }
    public function getFullInvoices($customerid = null)
    {
    	$dbh = Mage::helper('m3connection')->getM3Connection();
    	$invoices = new Varien_Data_Collection;
    
    	$sql = 'SELECT
		sum(if(ESTRCD=10,ESCUAM,0)) as "amount",
		sum(if(ESTRCD=10,ESCUAM,0)+if(ESTRCD=20,ESCUAM,0)) AS "outstanding",
		ESTRCD AS "status",
		ESCUNO AS "customernumber",
		ESCINO AS "invoicenumber",
		max(ESIVDT) AS "invoicedate",
		max(ESDUDT) AS "duedate"
		FROM FSLEDG
		WHERE ESCUAM <> 0
		AND ESARCD = 0
		AND ESCUNO = :id
		GROUP BY ESCINO, ESCUNO
		HAVING sum(if(FSLEDG.ESTRCD=10,FSLEDG.ESCUAM,0)+if(FSLEDG.ESTRCD=20,FSLEDG.ESCUAM,0)) <> 0
		ORDER BY ESIVDT DESC';
    
    	$sth = $dbh->prepare($sql);
    	$sth->bindParam(':id', $customerid, PDO::PARAM_STR, 12);
    	$sth->execute();
    	
    	$invoiceNumbers = array();
    	$tempInvoices = array();
    	while($invoice = $sth->fetch(PDO::FETCH_ASSOC)){
    		$invoiceNumbers[] = (int)$invoice['invoicenumber'];
    		$tempInvoices[$invoice['invoicenumber']] = $invoice;
    	}
    	 
    	//Load customer PO number
    	$poNumbers = $this->getCustomerPOS($customerid, $invoiceNumbers);
    	 
    	foreach ($tempInvoices as $invoiceNumber => $invoice) {
    		$invoiceNumber = intval($invoiceNumber);
    		$invoice['ponumber'] = $poNumbers[$invoiceNumber];
    		$invoices->addItem( new Varien_Object($invoice) );
    	}
    	
    	
    	return $invoices;
    }	
}
