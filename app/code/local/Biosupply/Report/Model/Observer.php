<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Report to newer
 * versions in the future.
 *
 * @category    Biosupply
 * @package     Biosupply_Report
 * @author     Extlab Team
 * @copyright   Copyright (c) 2015 Extlab Team (http://www.extlab.com)
 */
class Biosupply_Report_Model_Observer {
	public function loadCustomerNumber( $observer ) {
		//Load customer account number
		Mage::helper('bio_report')->getCustomerAccountNumber();
	}
	
	public function dailyExport() {
		Mage::log("Executing Daily Export", Zend_Log::DEBUG, 'exportReportController.log', true);
		$end_date = date('m/d/Y', strtotime ( '-1 day' , strtotime(date('m/d/Y')) ));
		
		$start_date = $end_date;
		//extra adding customerid,account no, more
		
		$params['from_date'] = $start_date;
		$params['to_date'] = $end_date;
		
		$params['mode'] = 'ALL'; //MONTHLY, PRODUCT_GROUP
		return $this->prepareExport($params);
	}
	
	public function weeklyExport() {
		Mage::log("Executing Weekly Export", Zend_Log::DEBUG, 'exportReportController.log', true);
		$end_date = date('m/d/Y', strtotime ( '-1 day' , strtotime(date('m/d/Y')) ));
		$start_date = date('m/d/Y', strtotime ( '-7 day' , strtotime ( $end_date ) ) );
		
		$params['from_date'] = $start_date;
		$params['to_date'] = $end_date;
		$params['mode'] = 'ALL';
		return $this->prepareExport($params);
	}
	
	public function monthlyExport() {
		Mage::log("Executing Monthly Export", Zend_Log::DEBUG, 'exportReportController.log', true);
		$start_date = date('m/d/Y', strtotime('first day of last month'));
		$end_date = date('m/d/Y', strtotime('last day of previous month'));
		
		$params['from_date'] = $start_date;
		$params['to_date'] = $end_date;
		$params['mode'] = 'ALL';
		return $this->prepareExport($params);
	}
	
	public function quaterlyExport() {
		Mage::log("Executing Quartely Export", Zend_Log::DEBUG, 'exportReportController.log', true);
		$end_date = date('m/d/Y', strtotime('last day of previous month'));
		$start_date = date('m/01/Y', strtotime('3 months ago'));
		
		$params['from_date'] = $start_date;
		$params['to_date'] = $end_date;
		$params['mode'] = 'ALL';
		return $this->prepareExport($params);
	}
	
	private function prepareExport($params) {
		Mage::log($params, Zend_Log::DEBUG, 'exportReportController.log', true);
		$reportExporterBlock = new Biosupply_Report_Block_Group_Report_Export();
		//$reportController = new Biosupply_Report_ReportController();
		//$params = $this->prepareParams();
		$fileName = $this->getExportFileName($params, 'group');
		$this->prepareExportFilterParams($reportExporterBlock, $params);
		$content = $reportExporterBlock->getCsv();
		//$reportController->_prepareDownloadResponse($fileName, $content);
		return array (
			'content' => $content,
			'filename' => $fileName
		);
	}
	
	private function prepareParams($fromDate, $endDate, $mode, $accountNo, $customerId) {
		$params = array();
		$params['mode'] = $mode;		
		$params['accountNo'] = $accountNo;
		$params['accountLastName'] = $this->getCustomerlastname($customerId);
		$params['customerid'] = $customerId;		
		$params['from_date'] = $fromDate;
		$params['to_date'] = $endDate;
		return $params;
	}
	
	private function getExportFileName($params, $type) {
		$filename = null;
		if('group' === $type){
			if(isset($params['mode']) && !empty($params['mode'])){
				$filename .= $params['mode'];
			}
			$filename .= '_Order_History_';
			if(isset($params['from_date']) && !empty($params['from_date'])){
				$filename .= $params['from_date'];
			}
			if(isset($params['to_date']) && !empty($params['to_date'])){
				$filename .= '_'.$params['to_date'];
			}
		}
		else {
			if(isset($params['productgroup']) && !empty($params['productgroup'])){
				$filename .= $params['productgroup'] . '_';
			}
			if(isset($params['Year']) && !empty($params['Year'])){
				$filename .= $params['Year']. '_';
			}
			if(isset($params['Month']) && !empty($params['Month'])){
				$filename .= $params['Month']. '_';
			}
			if(isset($params['UOM']) && !empty($params['UOM'])){
				$filename .= $params['UOM'];
			}
			$filename .= '_Order_History';
		}
		if (isset($params['export_type']) && 'excel' === $params['export_type']) {
			$filename .= '.xls';
		}
		else {
			$filename .= '.csv';
		}
		return str_replace(' ', '', $filename);
	}
	
	private function getDataDate($dateParams) {
		$dataDate = array();		
		if (isset($dateParams['from_date']) && !empty($dateParams['from_date']) ) {
			$dataDate['from'] = $dateParams['from_date'];
		}		
		if (isset($dateParams['to_date']) && !empty($dateParams['to_date']) ) {
			$dataDate['to'] = $dateParams['to_date'];
		}
		return $dataDate;
	}
	
	private function prepareExportFilterParams(&$block, $params = array()) {
		if ( $block) {
			$h = Mage::helper('bio_report');
			$block->setData('report_export', 'scheduler');
			$block->setData('date', $this->getDataDate($params) );
			if ( isset( $params['productgroup']) && !empty( $params['productgroup'])) {
				$block->setData('productgroup', $h->cleanString($params['productgroup']));
			}
			if (isset($params['Month']) && !empty($params['Month'])) {
				$block->setData('month', $h->cleanString($params['Month']));
			}
			if (isset($params['Year']) && !empty($params['Year'])) {
				$block->setData('year', $h->cleanString($params['Year']));
			}
			if (isset($params['UOM']) && !empty($params['UOM'])) {
				$block->setData('UOM', $h->cleanString($params['UOM']));
			}
			if (isset($params['mode']) && !empty($params['mode'])) {				
				$block->setMode($params['mode']);
				$block->setData('reportmode', $h->cleanString($params['mode']));
			}
			if ( isset( $params['accountNo']) && !empty( $params['accountNo'])) {
				$block->setData('accountNo', $h->cleanString($params['accountNo']));
			}
			if (isset($params['customerid']) && !empty($params['customerid'])) {
				$block->setData('customerid', $h->cleanString($params['customerid']));
			}
			if ( isset( $params['accountLastName']) && !empty( $params['accountLastName'])) {
				$block->setData('accountLastName', $h->cleanString($params['accountLastName']));
			}
			if ( isset( $params['accountLastName']) && !empty( $params['accountLastName'])) {
				$block->setData('accountLastName', $h->cleanString($params['accountLastName']));
			}			
		}
	}
	
	public function getCcemail()
	{
     return Mage::getStoreConfig("contacts/email/recipient_email");
	}
	public function getCustomerlastname($cust_id)
	{
	   $_lastname = Mage::getModel('customer/customer')->load($cust_id); //return customer object
	   return $_lastname->getLastname();
	  
	}
	
	public function getCustomeremail($cust_id)
	{
	   $_email = Mage::getModel('customer/customer')->load($cust_id); //return customer object
	   return $_email->getEmail();
	  
	}
	
	
	
	public function getBiovesionreportCollection()
	{
	   $_reportcollection = Mage::getModel("report/report")->getCollection();  //return the Report Collection
	   return $_reportcollection;
	}
	
	
	public function sendBiovisionreportdaily()   //observar method to run Daily cron
	{
	
    $collection = $this->getBiovesionreportCollection()
	                ->addFieldToFilter("type","Daily");  //report collection on daily mode
	                  
      $_rtype = array();
	
	  foreach($collection as $collection1)
	   {
	   $_rtype= explode(",",$collection1['report_type']); 
	  //echo '<pre>';
		//print_r($_rtype);				
		$end_date = date('m/d/Y', strtotime ( '-1 day' , strtotime(date('m/d/Y')) ));
		$start_date = $end_date;		
		$accountNo =  $collection1->getAccountNo();   //account no
		
		$_reportnoneoption = Mage::getModel("report/report")->getCollection()
		                     ->addFieldToFilter("type","none")
							 ->addFieldToFilter("account_no",$accountNo);
		
        if(count($_reportnoneoption) > 0)
		{
		}
		else
		{
        $_reportcollection = Mage::getModel("report/report")->getCollection(); 
		
		//$customerId = $this->getCustomerlastname($collection1->getCustomerid());
		$customerId = $collection1->getCustomerid();  //get customer  id
		
	    $customerEmail = $this->getCustomeremail($collection1->getCustomerid()); //customer email
	       foreach($_rtype as $_rtype2)  //each report type
					  { 
						  $mode = $this->reportModeResolver($_rtype2);
					
						  $params = $this->prepareParams($start_date, $end_date, $mode, $accountNo, $customerId);
						//extra adding customerid,account no, more
						 //return $this->prepareExport($params);
						 
					   $filename = $this->prepareExport($params)['filename'];
						 
					   $filecontent = $this->prepareExport($params)['content']['value'];

						 //email attachment  starts
							try{	
							$emailTemplate  = Mage::getModel('core/email_template')->loadByCode('Bio Vision Report Email Template');	

							$storeId = Mage::app()->getStore()->getId();
							$templateId = $emailTemplate['template_id'] ;
							$senderName  = Mage::getStoreConfig('trans_email/ident_support/name');
							$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

							$sender = array('name' => $senderName,'email' => $senderEmail);
							$vars = array(); 
							$vars['customer_name'] = " ".$customerName;
							$vars_email['customer_email'] =$customerEmail;

                            //$vars_email['customer_email'] = "kushagra.daharwal@zensar.in";
							$fileContents =    $filecontent;

							$file_contents = file_get_contents($fileContents);

							$transactionalEmail= Mage::getModel('core/email_template');
                            $ccemail =  $this->getCcemail();
							

                            $transactionalEmail->getMail()
							->createAttachment($file_contents,'text/csv')->filename = $filename;
                            $transactionalEmail->getMail()->addCc($ccemail); //cc email
						    $transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents);
							$transactionalEmail->setTranslateInline(true);

							if($transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents))
							{
							 echo "email is sending";
							   
							 }else
							 {
							  echo "email is not sending";
							   
							 }
											
							Mage::getSingleton('core/session')->addSuccess('Attachment Sent successfully.');
							}
							catch (Exception $e) {
							Mage::log('Email sending failed:  '.$e->getMessage(), null, 'PricelistAttachmentfaild.log');
							}

					}
	  
           }
		  
  	 }
	}
	
	private function reportModeResolver($reportType) {
		if ($reportType == 'group_history') {//daily_history,monthly_history,
			return 'PRODUCT_GROUP';
		}
		if ($reportType == 'monthly_history') {//daily_history,monthly_history,
			return 'MONTHLY';
		}
		if ($reportType == 'all_history') {//daily_history,monthly_history,
			return 'ALL';
		}
	}
	
	public function sendBiovisionreportweekly() //observar method to run Weely cron
	{
		$collection = $this->getBiovesionreportCollection()
	                ->addFieldToFilter("type","Weekly");  //report collection on daily mode
	                  
      $_rtype = array();
	
	  foreach($collection as $collection1)
	   {
	   $_rtype1= explode(",",$collection1['report_type']); 
	  
	   
	     //echo '<pre>';
		//print_r($_rtype);				
		$end_date = date('m/d/Y', strtotime ( '-1 day' , strtotime(date('m/d/Y')) ));
		$start_date = date('m/d/Y', strtotime ( '-7 day' , strtotime ( $end_date ) ) );
		$accountNo =  $collection1->getAccountNo();   //account no 
		//$customerId = $this->getCustomerlastname($collection1->getCustomerid());
		
		$_reportnoneoption = Mage::getModel("report/report")->getCollection()
		                     ->addFieldToFilter("type","none")
							 ->addFieldToFilter("account_no",$accountNo);
		if(count($_reportnoneoption) > 0)
		{
		}
		else
		{
		$customerId = $collection1->getCustomerid();  //get customer  id
		
	    $customerEmail = $this->getCustomeremail($collection1->getCustomerid()); //customer email
	
	 		  foreach($_rtype1 as $_rtype2)  //each report type
					  { 
						$mode = $this->reportModeResolver($_rtype2);
					
					
						 $params = $this->prepareParams($start_date, $end_date, $mode, $accountNo, $customerId);
						  		 
					     $filename = $this->prepareExport($params)['filename'];
						 
					     $filecontent = $this->prepareExport($params)['content']['value'];
						//extra adding customerid,account no, more
						 $this->prepareExport($params);
						 
						 //email attachment  starts
							try{	
							$emailTemplate  = Mage::getModel('core/email_template')->loadByCode('Bio Vision Report Email Template');	

							$storeId = Mage::app()->getStore()->getId();
							$templateId = $emailTemplate['template_id'] ;
							$senderName  = Mage::getStoreConfig('trans_email/ident_support/name');
							$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

							$sender = array('name' => $senderName,'email' => $senderEmail);
							$vars = array(); 
							$vars['customer_name'] = " ".$customerName;
							$vars_email['customer_email'] = $customerEmail;


							
						    $fileContents =    $filecontent;

							$file_contents = file_get_contents($fileContents);
							
							$transactionalEmail= Mage::getModel('core/email_template');
                            $ccemail =  $this->getCcemail();

							$transactionalEmail->getMail()
							->createAttachment($file_contents,'text/csv')->filename = $_collectiondaily['acclink'].".csv";
                            $transactionalEmail->getMail()->addCc($ccemail); //cc email
							$transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents);
							$transactionalEmail->setTranslateInline(true);	
							
							 if($transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents))
							 {
							   echo "email is sending";
							 }else
							 {
							   echo "email is not sending";
							 }
                             Mage::getSingleton('core/session')->addSuccess('Attachment Sent successfully.');
							}
							catch (Exception $e) {
							Mage::log('Email sending failed:  '.$e->getMessage(), null, 'PricelistAttachmentfaild.log');
							}

		            	}
	          }
	       }
	}
	
	
	
	public function sendBiovisionreportmonthly() //observar method to run Monthly cron
	{
		$collection = $this->getBiovesionreportCollection()
	                ->addFieldToFilter("type","Monthly");  //report collection on daily mode
	                  
      $_rtype = array();
	
	  foreach($collection as $collection1)
	   {
	   $_rtype = explode(",",$collection1['report_type']); 
	 
	     //echo '<pre>';
		//print_r($_rtype);				
		$start_date = date('m/d/Y', strtotime('first day of last month'));
		$end_date = date('m/d/Y', strtotime('last day of previous month'));
		$accountNo =  $collection1->getAccountNo();   //account no 
		//$customerId = $this->getCustomerlastname($collection1->getCustomerid());
		
		$_reportnoneoption = Mage::getModel("report/report")->getCollection()
		                     ->addFieldToFilter("type","none")
							 ->addFieldToFilter("account_no",$accountNo);
							 
	    if(count($_reportnoneoption) > 0)
		{
		}
		else
		{
		$customerId = $collection1->getCustomerid();  //get customer  id
		
	    $customerEmail = $this->getCustomeremail($collection1->getCustomerid()); //customer email
	
	   foreach($_rtype as $_rtype2)  //each report type
			  {
						 $mode = $this->reportModeResolver($_rtype2);
					
					
						 $params = $this->prepareParams($start_date, $end_date, $mode, $accountNo, $customerId);
						   		 
					     $filename = $this->prepareExport($params)['filename'];
						 
					     $filecontent = $this->prepareExport($params)['content']['value'];
						//extra adding customerid,account no, more
						  $this->prepareExport($params);
						 
						 //email attachment  starts
							try{	
							$emailTemplate  = Mage::getModel('core/email_template')->loadByCode('Bio Vision Report Email Template');	

							$storeId = Mage::app()->getStore()->getId();
							$templateId = $emailTemplate['template_id'] ;
							$senderName  = Mage::getStoreConfig('trans_email/ident_support/name');
							$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

							$sender = array('name' => $senderName,'email' => $senderEmail);
							$vars = array(); 
							$vars['customer_name'] = " ".$customerName;
							$vars_email['customer_email'] =$customerEmail;


							$fileContents =    $filecontent;

							$file_contents = file_get_contents($fileContents);

							$transactionalEmail= Mage::getModel('core/email_template');

                            $ccemail =  $this->getCcemail();
							$transactionalEmail->getMail()
							->createAttachment($file_contents,'text/csv')->filename = $_collectiondaily['acclink'].".csv";
                             $transactionalEmail->getMail()->addCc($ccemail); //cc email
							$transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents);
							$transactionalEmail->setTranslateInline(true);	
                            
							 if($transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents))
							 {
							   echo "email is sending";
							   die;
							 }else
							 {
							   echo "email is not sending";
							 }
							}
							catch (Exception $e) {
							Mage::log('Email sending failed:  '.$e->getMessage(), null, 'PricelistAttachmentfaild.log');
							}

		            	}
	          }
			  
          }
	}
	
	
	
	
	public function sendbiovisionreportquarterly() //observar method to run Quarterly cron
	{
		$collection = $this->getBiovesionreportCollection()
	                ->addFieldToFilter("type","Quarterly");  //report collection on daily mode
	                  
      $_rtype = array();
	
	  foreach($collection as $collection1)
	   {
	   $_rtype[]= explode(",",$collection1['report_type']); 
	  
	   }
	     //echo '<pre>';
		//print_r($_rtype);				
		$end_date = date('m/d/Y', strtotime('last day of previous month'));
		$start_date = date('m/01/Y', strtotime('3 months ago'));
		$accountNo =  $collection1->getAccountNo();   //account no 
		//$customerId = $this->getCustomerlastname($collection1->getCustomerid());
		$customerId = $collection1->getCustomerid();  //get customer  id
		
		$_reportnoneoption = Mage::getModel("report/report")->getCollection()
		                     ->addFieldToFilter("type","none")
							 ->addFieldToFilter("account_no",$accountNo);
		
	      if(count($_reportnoneoption) > 0)
		{
		}
		else
		{
		$customerEmail = $this->getCustomeremail($collection1->getCustomerid()); //customer email
	
	   foreach($_rtype as $_rtype1)  //each report type
			  {
			  foreach($_rtype1 as $_rtype2)  //each report type
					  { 
						

					
						 $mode = $this->reportModeResolver($_rtype2);
					
					
						 $params = $this->prepareParams($start_date, $end_date, $mode, $accountNo, $customerId);
						//extra adding customerid,account no, more
						  		 
					     $filename = $this->prepareExport($params)['filename'];
						 
					     $filecontent = $this->prepareExport($params)['content']['value'];
						 $this->prepareExport($params);
						 
						 //email attachment  starts
							try{	
							$emailTemplate  = Mage::getModel('core/email_template')->loadByCode('Bio Vision Report Email Template');	

							$storeId = Mage::app()->getStore()->getId();
							$templateId = $emailTemplate['template_id'] ;
							$senderName  = Mage::getStoreConfig('trans_email/ident_support/name');
							$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

							$sender = array('name' => $senderName,'email' => $senderEmail);
							$vars = array(); 
							$vars['customer_name'] = " ".$customerName;
							$vars_email['customer_email'] =$recieveremail;
							//$vars_email['customer_email'] = "kushagra.daharwal@zensar.in";


						
							$fileContents =    $filecontent;

							$file_contents = file_get_contents($fileContents);

							$transactionalEmail= Mage::getModel('core/email_template');

                            $ccemail  = $this->getCcemail();
							$transactionalEmail->getMail()
							->createAttachment($file_contents,'text/csv')->filename = $_collectiondaily['acclink'].".csv";
                             $transactionalEmail->getMail()->addCc($ccemail); //cc email
							$transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents);
							$transactionalEmail->setTranslateInline(true);	

							
							 if($transactionalEmail->sendTransactional($templateId, $sender, $vars_email, $customerName, $vars, $storeId, $file_contents))
							 {
							   echo "email is sending";
							 }else
							 {
							   echo "email is not sending";
							 }
							 
							}
							catch (Exception $e) {
							Mage::log('Email sending failed:  '.$e->getMessage(), null, 'PricelistAttachmentfaild.log');
							}

		            	}
	          }
			 }
	}
	

}