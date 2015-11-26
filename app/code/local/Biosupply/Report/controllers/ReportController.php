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
class Biosupply_Report_ReportController  extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->loadLayout();
			//$this->getLayout()->getBlock('head')->removeItem('skin_js','js/script.js');
			$this->renderLayout();
		} 
		else {
			$url = Mage::getUrl('customer/account/login');
			$this->_redirectUrl($url);
		}
	}
	public function loadreportAction() {
		$dateParams = $this->getRequest()->getParams();
		$layout = $this->getLayout();
		$block = $layout->createBlock('bio_report/group_datatable', 'bio.report.datatable')->setTemplate('biosupply/group/datatable.phtml');
		//$block->setMode(Biosupply_Report_Block_Group_Datatable::PRODUCT_GROUP);
		$block->setData('date', $this->getDataDate($dateParams));
		echo $block->toHtml();
		return;
	}
	public function getDataDate($dateParams) {
		$dataDate = array();
		$dataDate['from'] = Mage::helper('bio_report')->getDefaultToDate();
		if (isset($dateParams['from_date']) && !empty($dateParams['from_date']) ) {
			$dataDate['from'] = $dateParams['from_date'];
		}
		$dataDate['to'] = Mage::helper('bio_report')->getDefaultToDate();
		if (isset($dateParams['to_date']) && !empty($dateParams['to_date']) ) {
			$dataDate['to'] = $dateParams['to_date'];
		}
		return $dataDate;
	}
	public function groupexportAction() {
		$groupParams = $this->getRequest()->getParams();
		$fileName = $this->getFileName($groupParams, 'group');
		$layout = $this->getLayout();
		$block = $layout->createBlock('bio_report/group_report_export');
		$this->prepareFilterParams($block);
		if (isset($groupParams['export_type']) && 'excel' === $groupParams['export_type']) {
			$content = $block->getExcel($fileName);
		}
		else {
			$content = $block->getCsv();
		}
		$this->_prepareDownloadResponse($fileName, $content);
	}
	public function detailexportAction() {
		$detailParams = $this->getRequest()->getParams();
		$fileName = $this->getFileName($detailParams, 'detail');
		$layout = $this->getLayout();
		$block = $layout->createBlock('bio_report/group_report_export');
		$this->prepareFilterParams($block);
		$block->setMode(Biosupply_Report_Block_Group_Datatable::ALL);
		if (isset($detailParams['export_type']) && 'excel' === $detailParams['export_type']) {
			$content = $block->getExcel($fileName);
		}
		else {
			$content = $block->getCsv();
		}
		$this->_prepareDownloadResponse($fileName, $content);
	}
	public function getFileName($params, $type) {
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
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function versionAction() {
		$db = Mage::helper('bio_report')->getConnection();
		echo $db->getServerVersion();
		die();
	}
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function tablesAction() {
		$db = Mage::helper('bio_report')->getConnection();
		$tables = $db->listTables();
		print_r($tables);
		die();
	}
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function selectAction() {
		$data = array();
		$customerAccNumber = Mage::helper('bio_report')->getCustomerAccountNumber();
		if ( isset($customerAccNumber) && !empty($customerAccNumber)) {
			$connect = Mage::helper('biomssql')->getConnection();
			$select =  $connect->select()
			->from( array( 'il' => $connect->getTableName('Sales.InvoiceLine')))
			->join( array( 'ih' => $connect->getTableName('Sales.InvoiceHead') ),
					'il.InvoiceHeadId = ih.InvoiceHeadId')
					->where('"il".CustomerNumber = \'?\'', '2413');
			$data = $connect->fetchAll($select);
		}
		print_r($data);
		die();
	}
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function dataAction() {
		$data = array();
		$customerAccNumber = Mage::helper('bio_report')->getCustomerAccountNumber();
		if ( isset($customerAccNumber) && !empty($customerAccNumber)) {
			$connect = Mage::helper('biomssql')->getConnection();
			$select =  $connect->select()
			->from( array( 'il' => $connect->getTableName('Sales.InvoiceLine')))
			->join( array( 'ih' => $connect->getTableName('Sales.InvoiceHead') ),
					'il.InvoiceHeadId = ih.InvoiceHeadId')
					->where('"il".CustomerNumber = ?', $customerAccNumber);
			//die($select);
			$data = $connect->fetchAll($select);
		}
		print_r($data);die();
	}
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function dumpAction() {
		$io = new Varien_Io_File();
		$path = Mage::getBaseDir('var') . DS . 'export' . DS;
		$name = 'invoiceLine-'.md5(microtime());
		$file = $path . DS . $name . '.csv';
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $path));
		$io->streamOpen($file, 'w+');
		$io->streamLock(true);
		
		//Get data
		//$connect = Mage::helper('biomssql')->getConnection();
		$connect = $dbh = Mage::helper('m3connection')->getM3Connection();
		$select =  $connect->select()->from(array( 'il' => $connect->getTableName('Sales.InvoiceLine')));
		$items = $connect->fetchAll($select);
		
		$item = current($items);
		$headers = array_keys($item);
		
		$io->streamWriteCsv( $headers );
		foreach ($items as $itemArray) {
			$io->streamWriteCsv( $itemArray );
		}
		die($path.'/'.$file);
	}
	/**
	 * This is just demo function
	 * Can be remove when production
	 */
	public function importAction() {
		$csv=new Varien_File_Csv();
		$path = Mage::getBaseDir('var') . DS . 'export' . DS;
		$file=$path.'invoiceLine.csv';
		$invoiceLines=$csv->getData($file);
		$connect = Mage::helper('biomssql')->getConnection();
		$tableName = $connect->getTableName('Sales.InvoiceLine');
		foreach($invoiceLines as $line)
		{
			$db->insert ( $tableName, $line );
		}
		echo 'DONE';
		exit();
	}
	public function loaddetailAction() {
		$layout = $this->getLayout();
		//$this->getLayout()->getBlock('head')->removeItem('skin_js','js/script.js');
		$block = $layout->createBlock('bio_report/group_datatable', 'bio.report.detail')->setTemplate('biosupply/group/datatable/detail.phtml');
		$block->setMode('DETAIL');
		$this->prepareFilterParams($block);
		echo $block->toHtml();
		return;
	}
	public function prepareFilterParams(&$block, $params = array()) {
		
		if ( $block) {
			
			$h = Mage::helper('bio_report');
			
			$currentParams = $this->getRequest()->getParams();
			
			$params = array_merge($currentParams, $params);
			
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
			}
			
		}
	}
	
	public function savereporttypeAction() 
	{ 
	 $post = $this->getRequest()->getPost();  //post data
	 
     $report_history = implode(",",$post['report_history']); //comma seprated checkboxes values

     $model = Mage::getModel("report/report"); //report model instance
	 
	 $customer_id =  Mage::getSingleton('customer/session')->getCustomer()->getId();  //get customer id
	  
	 $customerData = Mage::getSingleton('customer/session')->getCustomer();  //get customer object
	   
	 $default_customeraccount = Mage::helper('customeraccount/customer')->getDefaultCustomeraccount($customerData);
	 
	 $customerAccountNo = $default_customeraccount['account_number'];
     
	 $insert_dataarray = array("customerid"=>$customer_id,"account_no"=>$customerAccountNo,"type"=>$post['biovisionreport'],"report_type"=>$report_history);  
     
	 $model->setData($insert_dataarray);
	 
	 $model->save();
	 
	 if($model->save())
	 {

	  	Mage::getSingleton('core/session')->addSuccess($this->__('Report Saved Successfully.'));
	    $this->_redirectUrl( Mage::getBaseUrl().'customer/account/edit/');
	 }
	 
	}
}
