<?php

class Mfv_History_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function generateBuyingReport($type, $fileName, $orderedval,$yearval,$dirvalue,$orderval) {
   
		$customerData = Mage::getSingleton('customer/session')->getCustomer();						
		
	    
		$orders = new Varien_Data_Collection;
        $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
   	    if($orderedval && !$yearval)
		{
		 $filter_collection = $this->generateOrderedreport($accid,$orderedval,$dirvalue,$orderval);
	     $filter_collectiondata = $filter_collection;
	     $buyingHistory = $this->getBuyingHistoryList($filter_collectiondata);
				if ($type == 'csv') {
				$this->generateBuyingReportListCsv($buyingHistory, $fileName);
				} else {
				//$this->generateBuyingReportListPdf($buyingHistory, $fileName);
				}
				return $data;

		}
		
		else if($yearval && !$orderedval)
		{
	        $filter_collection = $this->generateYearreport($accid,$yearval,$dirvalue,$orderval);
			$filter_collectiondata = $filter_collection;
	        $buyingHistory = $this->getBuyingHistoryList($filter_collectiondata);
			if ($type == 'csv') {
			$this->generateBuyingReportListCsv($buyingHistory, $fileName);
			} else {
			//$this->generateBuyingReportListPdf($buyingHistory, $fileName);
			}
			return $data;
		
		}
		
		else if($orderedval && $yearval)
		{
		  echo $orderedval ."year".$yearval;
			$filter_collection = $this->generateBothparamreport($accid,$orderedval,$yearval,$dirvalue,$orderval);
			$filter_collectiondata = $filter_collection;
	        $buyingHistory = $this->getBuyingHistoryList($filter_collectiondata);
			if ($type == 'csv') {
			$this->generateBuyingReportListCsv($buyingHistory, $fileName);
			} else {
			//$this->generateBuyingReportListPdf($buyingHistory, $fileName);
			}
			return $data;
		
		}
		else
		{
		
		$filter_collection = $this->generateDefaultreport($accid,$dirvalue,$orderval);
		$filter_collectiondata = $filter_collection;
	    $buyingHistory = $this->getBuyingHistoryList($filter_collectiondata);
		
		//echo '<pre>';print_r($buyingHistory);die;
		
	    if ($type == 'csv') {
		$this->generateBuyingReportListCsv($buyingHistory, $fileName);
		} else {
			//$this->generateBuyingReportListPdf($buyingHistory, $fileName);
		}
		return $data;
		
		}
		
			//	echo '<pre>';print_r(return $filter_collection );die;

				
			
	}	
	
	public function generateDefaultreport($_accid,$dir,$order)
	{
	    $dbh = Mage::helper('m3connection')->getM3Connection();
	    $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
		$cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
		if($order != null || $dir != null )
		{
	    $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE   OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.' ';
        }else{
		 $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE   OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
		}
			
		$collect = $dbh->query($sql);
		 
		$val =  $collect->fetchAll();
		return $val;
		
		
	}
	
	public function generateOrderedreport($accid,$ordered,$dir,$order)
	{
       $dbh = Mage::helper('m3connection')->getM3Connection();	   
	   $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
	   $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
	  if($order != null || $dir != null )
		{		
	    $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE  OAORST = "'.$ordered.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.'';
       }else {
	   
	    $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE  OAORST = "'.$ordered.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
              }	   
	   $collect = $dbh->query($sql);
		 
		$val =  $collect->fetchAll();
		 
		return $val;
		
	}
	
	public function generateYearreport($accid,$year,$dir,$order)
	{
	 $dbh = Mage::helper('m3connection')->getM3Connection();
	 $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
	 $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";

	
if(!empty($order) || !empty($dir))
		{	
		 $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE  MMFRE3 = "'.$year.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.''; 
    }else{
	      
		   $sql .= 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
			WHERE  MMFRE3 = "'.$year.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
			
	    }
		//echo $sql;
		//die;
		$collect = $dbh->query($sql);
		 
		$val =  $collect->fetchAll();
		
     //echo '<pre>';print_r($val);		
		return $val;
	}
	
	public function generateBothparamreport($accid,$ordered,$year,$dir,$order)
	{
	 $dbh = Mage::helper('m3connection')->getM3Connection();
	 $default_customeraccount = Mage::helper('customcustomer')->getCustomerAccountId();
	 $cuno = $default_customeraccount->getAccountNumber();
		//$cuno = "FAZ02348A";
		if($order != null || $dir != null )
		{
			echo $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
			INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
			INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
			WHERE  MMFRE3 = "'.$year.'" AND OAORST="'.$ordered.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY '.$order.' '.$dir.'';
			
		}else
       {		
	       $sql = 'SELECT OAORNO AS "orderNumber", OACUOR AS "poNumber", MMFRE3 AS "Season", OAORDT AS "orderDate", 
			OAORST AS "orderStatus", OACUNO AS "accountNumber", OBITNO AS "itemNumber", OBITDS AS "prodDesc", 
			OBORQT AS "qtyOrdered",MMUNMS AS "StockUoM", OBNEPR AS "priceEach", OBLNA2 AS "lineTotal",OBORQA AS "AltUoM",
			OBALUN AS "Doses" , OBFDED AS "deliveryDate"
			FROM OOHEAD
					  INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
				INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		
		    WHERE  MMFRE3 = "'.$year.'" AND OAORST="'.$ordered.'" AND OACONO = 1 AND OACUNO = "'.$cuno .'" AND (OAORST >=22 AND OAORST<=77)
			AND OAORTP LIKE ("'.'MF%%'.'")
			ORDER BY OAORNO DESC';
         }
		//echo $sql  ;
	    //die;
		$collect = $dbh->query($sql);
		 
		$val =  $collect->fetchAll();
		 
		return $val;
		
	}
	
	public function generateBuyingReportSeason($type, $fileName) {
		$customerData = Mage::getSingleton('customer/session')->getCustomer();						
		
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
		$filter_collection =  $collecttion;
		
		foreach($filter_collection as $filter_collection1)
		{
		   $season[] =  $filter_collection1['Season'];
		 
		}
	    $sesaonsdata = implode(",",$season);	
   
	   $sql = 'SELECT MMFRE3 AS "Season" , OBITNO AS "itemNumber", OBITDS AS "prodDesc", sum(OBORQT) AS "qtyOrdered" 
		FROM OOHEAD
		INNER JOIN OOLINE ON (OACONO = OBCONO AND OAORNO = OBORNO)
		INNER JOIN MITMAS ON (OBCONO = MMCONO AND OBITNO = MMITNO)
		WHERE MMFRE3 in '.'('.$sesaonsdata.')'.' AND OACONO = 1 AND OACUNO = "'.$cuno.'"  AND (OAORST >=22 AND OAORST<=77)
		AND OAORTP LIKE ("'.'MF%%'.'") group by OBITNO
		ORDER BY OAORNO DESC ';
  
	    $collect = $dbh->query($sql);
		 
		$collecttion =  $collect->fetchAll();
      
	    $buyingHistory = $this->getBuyingHistoryListseason($collecttion);
		if ($type == 'csv') {
			$this->generateBuyingReportListCsvseason($buyingHistory, $fileName);
		} else {
			$this->generateBuyingReportListPdf($buyingHistory, $fileName);
		}
		//return $data;		
	}	
	
	
	public function generateBuyingReportListCsv($buyingHistory, $fileName) {
	

		//$pdfData = $this->generatePdf($priceListData, $customerAccountName." - ".$customerAccountNo, $customerAccountNo . '.pdf');
		$csvFile = 
  			$this->generateCsv($buyingHistory, $this->getQualifiedName($fileName, 'csv', true));
		return $csvFile;
	}
		
	public function generateBuyingReportListCsvseason($buyingHistory, $fileName) {
		//$pdfData = $this->generatePdf($priceListData, $customerAccountName." - ".$customerAccountNo, $customerAccountNo . '.pdf');
		$csvFile = 
  			$this->generateCsvseason($buyingHistory, $this->getQualifiedName($fileName, 'csv', true));
		return $csvFile;
	}
	
	
	private function generateCsv($buyingHistoryList, $fileName) {
		try {
			$configOptions = new Mage_Core_Model_Config_Options();
			$configOptions->createDirIfNotExists($this->getFileDir());
			$fileHandle = fopen($fileName, "w");
			fputcsv($fileHandle, $this->getBuyingHistoryReportHeader());
			foreach ($buyingHistoryList as $buyingHistory) {

				$productLine = array();
				foreach($buyingHistory as $productKey=>$prouctDetail) {
					$productLine[] = $prouctDetail;
				}
				
				Mage::log(implode(',', $productLine), Zend_Log::DEBUG, 'buying-history-list-csv-generation.log', true);
				
                fputcsv($fileHandle, $productLine);
			}
			fclose($fileHandle);
		} catch(Exception $error) {
			Mage::log('Qualified File Name [' . $fileName . '] Message[' . $error->getMessage() . ']', Zend_Log::DEBUG, 'buying-history-list-csv-generation.log', true);
		}
		return $fileHandle;		
	}
	
	private function generateCsvseason($buyingHistoryList, $fileName) {
		try {
			$configOptions = new Mage_Core_Model_Config_Options();
			$configOptions->createDirIfNotExists($this->getFileDir());
			
			$fileHandle = fopen($fileName, "w");
			fputcsv($fileHandle, $this->getBuyingHistoryReportHeaderSeason());
			foreach ($buyingHistoryList as $buyingHistory) {

				$productLine = array();
				foreach($buyingHistory as $productKey=>$prouctDetail) {
					$productLine[] = $prouctDetail;
				}
				Mage::log(implode(',', $productLine), Zend_Log::DEBUG, 'buying-history-list-csv-generation.log', true);
				fputcsv($fileHandle, $productLine);
			}
			fclose($fileHandle);
		} catch(Exception $error) {
			Mage::log('Qualified File Name [' . $fileName . '] Message[' . $error->getMessage() . ']', Zend_Log::DEBUG, 'buying-history-list-csv-generation.log', true);
		}
		return $fileHandle;		
	}
	
	private function getBuyingHistoryList($buyingHistoryCollection) {		
 		$buyingHistory = array(); 
        
		foreach ($buyingHistoryCollection as $buyingHistoryInd) {
		

            $buyingHistoryTmp['orderNumber'] = $this->cleanData($buyingHistoryInd['orderNumber']);
	
			
            $buyingHistoryTmp['poNumber'] = $this->cleanData($buyingHistoryInd['poNumber']);
        
    		$buyingHistoryTmp['orderDate'] = $this->cleanData(Mage::helper('core')->formatDate($buyingHistoryInd["orderDate"], Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false));        	
 			
			$buyingHistoryTmp['orderStatus'] = $this->cleanData($buyingHistoryInd['orderStatus']);
	        $buyingHistoryTmp['accountNumber'] = $this->cleanData($buyingHistoryInd['accountNumber']);
			$buyingHistoryTmp['prodDesc'] = $this->cleanData($buyingHistoryInd['prodDesc']);
			$buyingHistoryTmp['deliveryDate'] = $this->cleanData(Mage::helper('core')->formatDate($buyingHistoryInd["deliveryDate"], Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, false));
	        
			$buyingHistoryTmp['qtyOrdered'] = $this->cleanData($buyingHistoryInd['qtyOrdered']);
	        $buyingHistoryTmp['priceEach'] = $this->cleanData($buyingHistoryInd['priceEach']);
			$buyingHistoryTmp['lineTotal'] = $this->cleanData($buyingHistoryInd['lineTotal']);	        
            $buyingHistory[] = $buyingHistoryTmp;        	
        }
			//echo '<pre>';
		//	print_r($buyingHistory);
			//die;
		     // $buyingHistory[] = array( $buyingHistoryTmp['orderNumber'] , $buyingHistoryTmp['poNumber'], $buyingHistoryTmp['orderDate'] , '4', '5', '6', '7', '8', '9', '10');
			  
			//$buyingHist[] = $buyingHistory;
           
		
		//echo '<pre>';print_r($priceList);
		//die;
        return $buyingHistory;
	}
	
	private function getBuyingHistoryListseason($buyingHistoryCollection) {		
 		$buyingHistory = array(); 
        
		foreach ($buyingHistoryCollection as $buyingHistoryInd) {
		

           $buyingHistoryTmp['season'] = $this->cleanData($buyingHistoryInd['Season']);
		        $buyingHistoryTmp['itemNumber'] = $this->cleanData($buyingHistoryInd['itemNumber']);
				     $buyingHistoryTmp['prodDesc'] = $this->cleanData($buyingHistoryInd['prodDesc']);
		     $buyingHistoryTmp['qtyOrdered'] = $this->cleanData($buyingHistoryInd['qtyOrdered']);
		
            $buyingHistory[] = $buyingHistoryTmp;        	
        }
			
        return $buyingHistory;
	}
	
	
	
	
	private function cleanData($data) {
		/*if (empty($data) || strtolower($data) == 'no') {
			$data = 'N/A';
		}*/
		return $data;
	}
	
	public function getBuyingHistoryReportHeader() {
		return array('Order Number', 'PO Number', 'Order Date', 'Order Status', 'Account Number', 
				'Product Description', 'Delivery Date', 'Quantity Ordered', 'Price', 'Total');
	}
	
	public function getBuyingHistoryReportHeaderSeason() {
		return array('Season','itemNumber','prodDesc','qtyOrdered');
	}
	
	
	private function generateBuyingReportListPdf($buyingHistory, $fileName) {
		//$pdfData = $this->generatePdf($priceListData, $customerAccountName." - ".$customerAccountNo, $customerAccountNo . '.pdf');
		$pdfData = 
  			$this->generatePdf($buyingHistory, $this->getFileName($fileName, 'pdf'));
		return $pdfData;
	}
	
	private function generatePdf($input, $pdfName) {
		$configOptions = new Mage_Core_Model_Config_Options();
		//$configOptions->createDirIfNotExists(Mage::getBaseDir('media').DS.'pricelist');
		//$configOptions->createDirIfNotExists(Mage::getBaseDir('media').DS.'pricelist'.DS.date(Ymd));
		//$configOptions->createDirIfNotExists($this->getPricePdfFileParentDir());
		$configOptions->createDirIfNotExists($this->getFileDir());
		//Mage::getModel('core/config_options')->createDirIfNotExists('pricelist');
		//Mage::getModel('core/config_options')->createDirIfNotExists(date(Ymd));
		
		//$pdf = new My_Pdf_Document($pdfName, Mage::getBaseDir('media').DS.'pricelist'.DS.date(Ymd));
		$pdf = new My_Pdf_Document($pdfName, $this->getFileDir());
		
		$pdf = $this->pageSetup($pdf);
  		$page = $pdf->createPage();
  		//$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER), 20);
		$pdfdata .= '<div class="or_history"><table class="blu_table  buyinghistory order_Confirm_table">';
     $obj = new FFF_Buyinghistory_Block_Orderseason();
$default_year = $obj->getDefaultyear();
             $i=1;
			 foreach($default_year  as $default_year1)
			 {
			   $season =  $default_year1['Season'];
			   
$pdfdata .=	'<thead>';
$pdfdata .=	 '<tr>
              <th colspan="2" class="">Flu Season';
$pdfdata .=	$season;
	$pdfdata .=			  '-';
	$pdfdata .= $season+1;
			  
		    if($i ==1) {
	$pdfdata .= " (current) ";
			}
			
			
			
  $pdfdata .='</th>';

  $pdfdata .=	 '</tr>';
  $pdfdata .=	'<br>';
    $pdfdata .=	'<br>';
   $pdfdata .=	      ' </thead>';

   $pdfdata .=	     '<tbody>';
   
  $_data =  $obj->seasoncollectiondata($season);
           
//echo '<pre>';
//print_r($this->seasoncollectiondata($season));
            foreach($_data as $_datafield)
	{

	$pdfdata .=	   '<tr>';
            
    $pdfdata .= '<td width="50%" class=""><span class="margin_l_40">';
	
	$pdfdata .=  $_datafield['itemNumber'].'-';
    $pdfdata .= $_datafield['prodDesc'];
	
	$pdfdata .= '</span></td>';
              
		$pdfdata .= '<td class=" "> <span class="margin_l_40">';
		
	$pdfdata .=  number_format($_datafield['qtyOrdered'],0);
	
	$pdfdata .= 'Doses</span></td>
              
            </tr>';
			
    }
	
	    $pdfdata .='<tr>';
            
       $pdfdata .=   '<td class="">  <span class="margin_l_40"></span></td>';
         $pdfdata .=      '<td class=" "> <span class="margin_l_40"></span></td>';
              
         $pdfdata .=   '</tr>';
			 
		$priceqty = $obj->seasoncollectionqty($season);
         foreach($priceqty as $_priceqty) {
           $pdfdata .=  '<tr>';
            
          $pdfdata .=    '<td class="bold">  <span class="margin_l_40">Total Quantity</span></td>';
		  
          $pdfdata .=    '<td class="bold"><span class="margin_l_40">';
		  $pdfdata .=  number_format($_priceqty['qtyOrdered'],0);

		   $pdfdata .= 'Doses</span></td>
              
            </tr>
            
             <tr>
            
              <td class="bold">  <span class="margin_l_40">Total Price</span></td>';
			  
             $pdfdata .=   '<td class="bold"><span class="margin_l_40">$';
			  $pdfdata .= number_format($_priceqty['priceEach'],0);
			  $pdfdata .= '</span></td>
              
            </tr>';
			    $pdfdata .=	'<br>';
				    $pdfdata .=	'<br>';
			
          }
			
			
   $pdfdata .=	     '</tbody>';
   
   $i++;	}
			 
		
			
     $pdfdata .=    '</table>
      
      
      </div>
      
   </div>
  </div>';
		

$input = $pdfdata ;

		$pdf->addPage($this->drawPageBody( $page, $input ));
		
		//$pdf->setHeader($this->drawPdfHeader($headerName));
		//$pdf->setFooter($this->drawPdfFooter());
		$pdf->save();
		
		$pdfData = $pdf->render();
		return $pdfData;		
	}
	private function array_msort($array, $cols)
	{
	    $colarr = array();
	    foreach ($cols as $col => $order) {
			$colarr[$col] = array();
	        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	    }
	    $eval = 'array_multisort(';
	    foreach ($cols as $col => $order) {
	        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
	    }
	    $eval = substr($eval,0,-1).');';
	    eval($eval);
	    $ret = array();
	    foreach ($colarr as $col => $arr) {
	        foreach ($arr as $k => $v) {
	            $k = substr($k,1);
	            if (!isset($ret[$k])) $ret[$k] = $array[$k];
	            $ret[$k][$col] = $array[$k][$col];
	        }
	    }
	    return $ret;
	
	}
	
	private function pageSetup($pdf) {
		$pdf->setMargin(My_Pdf::BOTTOM, 87);
		$pdf->setMargin(My_Pdf::TOP, 45);
		$pdf->setMargin(My_Pdf::LEFT, 20);
		$pdf->setMargin(My_Pdf::RIGHT, 20);
		return $pdf;
	}
	
	private function drawPageBody($page, $input) {
		$table = $this->drawTable($this->getBuyingHistoryReportHeaderSeason(), $input);
		$page->addTable($table, 10, 10);
		return $page;
	}
	
	private function drawTable($headerValues, $input) {
		$table = new My_Pdf_Table();
		/*Header*/
		$header_row = new My_Pdf_Table_Row();
		$header_cols = array();
		
		foreach ($headerValues as $k=>$v) {
			$header_col = new My_Pdf_Table_Column();			
			$header_col->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER_BOLD), 6);
			$header_col->setAlignment(My_Pdf::CENTER);
			$header_col->setText($v);
			//$header_col->setHeight(7);
			$header_cols[] = $header_col;
		}
		$header_row->setColumns($header_cols);		
		$header_row->setCellPaddings(array(1,1,4,1));
		//$header_row->setHeight(6);
		$table->setHeader($header_row);   
		
		$rowCount = 0; 
		foreach ($input as $key => $record) {
			$rowCount ++;
			$row = new My_Pdf_Table_Row();
			$row->setHeight(6);
			$cols = array();
			$firstRecord = true;
			$count = 0;
			foreach ($record as $k => $v) {
				$count ++;	
				$col = new My_Pdf_Table_Column();
				$col->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER), 6);
				//$col->setHeight(7);				
				//if ($firstRecord) {
				if ($k == 'orderNumber') {
					$col->setWidth(80);
					$col->setAlignment(My_Pdf::LEFT);
				}
				if ($k == 'prodDesc') {
					$col->setWidth(100);
					$col->setAlignment(My_Pdf::LEFT);
				}
				//if ($k == 'product_size' || $k == 'ndc' || $k == 'price' || $k == 'your_price') {
				if ($k == 'orderDate' || $k == 'orderStatus' || $k == 'accountNumber' || $k == 'poNumber' 
					|| $k == 'deliveryDate' || $k == 'qtyOrdered' || $k == 'priceEach' || $k == 'lineTotal') {					
					$col->setWidth(75);
					$col->setAlignment(My_Pdf::CENTER);
				}
				
				$col->setText($v);
				$firstRecord = false;	
				$cols[] = $col;      
			}
			$row->setColumns($cols);
	    
			//$row->setFont($font, 14);
			$row->setBorder(My_Pdf::TOP, new Zend_Pdf_Style());
			$row->setBorder(My_Pdf::BOTTOM, new Zend_Pdf_Style());
			$row->setBorder(My_Pdf::LEFT, new Zend_Pdf_Style());
			$row->setBorder(My_Pdf::RIGHT, new Zend_Pdf_Style());		    
	    	//top,right,bottom,left
			$row->setCellPaddings(array(1, 3, 3, 2));
			$table->addRow($row);
		}

		return $table;

	}

	public function getQualifiedName($fileName, $type) {
		$filePath = $this->getFileDir() . DS . $this->getFileName($fileName, $type);
		return $filePath;		
	}
	
	private function getFileParentDir() {
		$path = Mage::getBaseDir('media') . DS. 'buyinghistory';
		return $path;		
	}
	
	public function getFileDir() {
		$path = $this->getFileParentDir(). DS . date('Ymd');
		return $path;		
	}
	
	public function getDownloadedReportUrl($fileName, $type) {
		return Mage::getBaseUrl('media') . 'buyinghistory/' . date('Ymd') . '/' . $this->getFileName($fileName, $type);
	}	
	
	public function getFileName($fileName, $type) {
		return rawurldecode($fileName) . '.' . $type;
	}
}	
