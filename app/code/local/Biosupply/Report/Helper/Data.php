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
class Biosupply_Report_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getConnection() {
		return Mage::getSingleton ( 'core/resource' )->getConnection ( 'biomssql_write' );
	}
	/**
	 * Return Customer Number of current Logged in customer
	 * @throws Exception
	 * @return string
	 */
	public function getCustomerAccountNumber() {
		$accNumber = Mage::getSingleton('customer/session')->getReportCustomerNumber();
		$defaultCustomerAccount = Mage::helper('customcustomer')->getCustomerAccountId();
		if (Mage::getSingleton('customer/session')->isLoggedIn() && isset($defaultCustomerAccount)) {
			$customerData = Mage::getSingleton('customer/session')->getCustomer();
			$defaultCustomerAccount = Mage::helper('customeraccount/customer')->getDefaultCustomeraccount($customerData);
			$accNumber = $defaultCustomerAccount->getAccountNumber();
		}
		return $accNumber;
	}
	
	protected function getDateFromUrl() {
		$requestData = Mage::app()->getRequest()->getParams();
		$rs = array();
		if(!empty( $requestData['from_date'] ) ){
			$rs['from'] = $requestData['from_date'];
		}
		
		if(!empty($requestData['to_date'] ) ){
			$rs['to'] = $requestData['to_date'];
		}
		return $rs;
	}
	
	public function getDefaultFromDate() {
		$fromDate = '';
		$dateData = $this->getDateFromUrl();
		
		if( !isset($dateData['from'] ) ) {
			$timestamp = Mage::getModel('core/date')->timestamp('-1 month');
			//$timestamp = Mage::getModel('core/date')->timestamp(strtotime("-1 month"));
			//$firstDayOfPrevMonth = mktime(0, 0, 0, date("m",$timestamp),1, date("Y",$timestamp));
			//return date('m/d/Y', $firstDayOfPrevMonth);
			//$fromDate = Mage::getModel('core/date')->date('m/d/Y', $timestamp);
			$m = date('n');
			$fromDate = date('m/d/Y',mktime(1,1,1,$m-1,1,date('Y')));
		} else {
			$fromDate = $dateData['from'];
		}
		return $fromDate;
	}
	
	public function getDefaultToDate() {
		//$timestamp = Mage::getModel('core/date')->timestamp('+1 month');
		$toDate = '';
		$dateData = $this->getDateFromUrl();
		if( !isset($dateData['to'] ) ) {
			//$timestamp = Mage::getModel('core/date')->timestamp();
			//$toDate = Mage::getModel('core/date')->date('m/d/Y', $timestamp);
			$m = date('n');
			$toDate = date('m/d/Y',mktime(1,1,1,$m,0,date('Y')));
		} else {
			$toDate = $dateData['to'];
		}
		return $toDate;
		//return Mage::getModel('core/date')->date('m/d/Y',mktime(-1, 0, 0, date("m"), 1, date("Y")));
	}
	public function cleanString($str) {
		$stringHelper = Mage::helper('core/string');
		return $stringHelper->cleanString(trim($str));
	}
	
	public function cleanMode( $mode ){
		return trim( preg_replace("/[\/\&%#\$]/", "", $mode ) );
		//return strtolower(trim( preg_replace("/[\/\&%#\$]/", "", $mode ) ));
	}
    
	public function getBiovesionreportCollection()
	{
	   $_reportcollection = Mage::getModel("report/report")->getCollection();  //return the Report Collection
	   return $_reportcollection;
	}
	
    public function selectedbiovisionreporttype(){
		$customerId =  Mage::getSingleton('customer/session')->getCustomer()->getId();
		$customerData = Mage::getSingleton('customer/session')->getCustomer();
		$default_customeraccount = Mage::helper('customeraccount/customer')->getDefaultCustomeraccount($customerData);
		$customerAccountNo = $default_customeraccount['account_number'];
		$_collection = Mage::getModel('report/report')->getCollection()
								->addFieldToFilter("account_no",$customerAccountNo)
								->addFieldToFilter("customerid",$customerId)
								->addFieldToSelect(array("type","report_type","customerid","account_no"));
			
		
		
		$_collectionarray = array();
		//foreach($_collection as $_collection1)
		//{
		   //$_collectionarray[] =  $_collection1;
		  
		//}
		
		return  $_collection->getLastItem();
	}
	

	
	   
}