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
class Biosupply_Report_Block_Group_Datatable  extends Mage_Core_Block_Template {
	const PRODUCT_GROUP = 'PRODUCT_GROUP';
	const MONTHLY = 'MONTHLY';
	const ALL = 'ALL'; 
	protected $_mode = self::PRODUCT_GROUP;
	protected $_headerString = '';
	protected $_detailHeader = '';
	
	public function __construct() {
		$mode = $this->getRequest()->getParam('mode');
		$mode = Mage::helper('bio_report')->cleanMode($mode);
		switch ($mode) {
			case 'monthly':
				$this->_mode = self::MONTHLY;
				$this->_headerString = $this->__('Purchase History by Month');
				$this->_detailHeader = $this->__('Detail for Selected Month ');
				break;
			case 'all':
				$this->_mode = self::ALL;
				$this->_headerString = $this->__('Product Purchase History');
				break;
			default:
				$this->_mode = self::PRODUCT_GROUP;
				$this->_headerString = $this->__('Purchase History by Product Group');
				$this->_detailHeader = $this->__('Detail for Selected Product Group ');
			break;
		}
		//$this->setCollection($this->getCollection());
	}
	
	public function getHeaderString(){
		return $this->_headerString;
	}
	
	public function getDetailHeader(){
		return $this->_detailHeader;
	}
	
	protected function prepareDefaultDate() {
		$date = array();
		$dataDate = $this->getData('date');
		if (!isset($dataDate) || empty($dataDate)) {
			$date['from'] = Mage::helper('bio_report')->getDefaultFromDate();
			$date['to'] = Mage::helper('bio_report')->getDefaultToDate();
			$this->setData('date', $date);
		}
		return $this;
	}
	public function setMode($mode) {
		$this->_mode = $mode;
	}
	public function getExportDataUrl() {
		return  Mage::getUrl("bio_report/report/export");
	}
	public function getAction() {
		return Mage::getUrl("bio_report/report/groupexport");
	}
	public function getLoadDetailProduct() {
		return Mage::getUrl("bio_report/report/loaddetail");
	}
	public function getDetailAction() {
		return Mage::getUrl('bio_report/report/detailexport');
	}
	protected function getFromDate($format = 'd/m/Y') {
		$timestamp = Mage::getModel('core/date')->timestamp(time());
		$value = Mage::getModel('core/date')->date($format, $timestamp);
		if ( ($date = $this->getData('date')) && isset($date['from']) ) {
			$dateFrom = $date['from'] . ' 01:00:00';
			//$dateFrom = str_replace('/', '-', $dateFrom);
			$timestamp = Mage::getModel('core/date')->timestamp($dateFrom);
			$timestamp = strtotime($dateFrom);
			$value = Mage::getModel('core/date')->date($format, $timestamp);
			$value = date($format, $timestamp);
		}
		return $value;
	}
	
	protected function getToDate($format = 'd/m/Y') {
		$timestamp = Mage::getModel('core/date')->timestamp(time());
		$value = Mage::getModel('core/date')->date($format, $timestamp);
		if ( ($date = $this->getData('date')) && isset($date['to']) ) {
			$dateTo = $date['to'] . ' 24:59:59';
			$dateTo = $date['to'] . ' 01:00:00';
			//$dateTo = str_replace('/', '-', $dateTo);
			$timestamp = Mage::getModel('core/date')->timestamp($dateTo);
			$timestamp = strtotime($dateTo);
			$value = Mage::getModel('core/date')->date($format, $timestamp);
			$value = date($format, $timestamp);
		}
		return $value;
	}
	
	public function getFromYear() {
		return $this->getFromDate('Y');
	}
	
	public function getToYear() {
		return $this->getToDate('Y');
	}
	
	protected function applyYearFilter(&$select) {
		$fromYear = $this->getFromYear();
		$toYear = $this->getToYear();
		
		if ( $fromYear == $toYear ) {
			$select->where('ih.Year = ?', $toYear);
		} else {
			$select->where('ih.Year >= ? AND ih.Year <= ?', $fromYear, $toYear);
		}
	}
	
	public function getFromMonth() {
		return $this->getFromDate('m');
	}
	
	public function getToMonth() {
		return $this->getToDate('m');
	}
	
	protected function applyMonthFilter(&$select) {
		$fromMonth = $this->getFromMonth();
		$toMonth = $this->getToMonth();
	
		$select->where('ih.InvoiceMonth >= ? AND ih.InvoiceMonth <= ?', $fromMonth, $toMonth);
	}
	
	public function getFromDay() {
		return $this->getFromDate('d');
	}
	
	public function getToDay() {
		return $this->getToDate('d');
	}
	
	protected function applyDateRangeFilter(&$select) {
		$from = $this->getFromYear().$this->getFromMonth().$this->getFromDay();
		$to = $this->getToYear().$this->getToMonth().$this->getToDay();
	
		$select->where('ih.InvoiceDateNum >= '.$from); 
		$select->where('ih.InvoiceDateNum <= '.$to);
	}
	
	/**
	 * The main get collection of items for template
	 * @return multitype:
	 */
	public function getCollection(){
		$data = array();
		$this->prepareDefaultDate();		
		//die ("FROM >> " . $this->getData('date')['from'] . " TO >> " . $this->getData('date')['to']);
		$customerAccNumber = '';
		if ($this->isSchedulerReport()) {
			$customerAccNumber = $this->getData('accountNo');
		} else {
			$customerAccNumber = Mage::helper('bio_report')->getCustomerAccountNumber();
		}
		if ( isset($customerAccNumber) && !empty($customerAccNumber)) {
			//BY PRODUCT GROUP
			if ($this->_mode == 'PRODUCT_GROUP') {
				//$data = $this->getByProductGroupCollection($customerAccNumber);
			//BY MONTHLY
			} else if ($this->_mode == 'MONTHLY') {
				//$data = $this->getByMonthlyCollection($customerAccNumber);
			//BY ALL
			} else {
				//$data = $this->getInvoicesCollection($customerAccNumber);
			}
		}
		return $data;
	}
	/**
	 * Collection group by ProductGroup
	 */
	public function getByProductGroupCollection ( $customerAccNumber ) {
		$connect = Mage::helper('biomssql')->getConnection();
		$select =  $connect->select()
		->from( array( 'ih' => $connect->getTableName('Sales.InvoiceHead')),
				array(
						'ProductGroup' => 'il.ProductGroup',
						'ProductGroupName' => 'il.ProductGroupName',
						'Year' => 'ih.Year',
						'Qty' => 'SUM(il.InvoicedQuantitySalesPriceUM)',
						'UOM' => 'il.SalesPriceUnitOfMeasure',
						'InvoiceAmount' => 'SUM(ih.InvoiceAmount)'
				)
		)
		//->join( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceHeadId = ih.InvoiceHeadId', array())
		->joinLeft( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceNumber = ih.InvoiceNumber', array())
		//->where('il.CustomerNumber = ?', $customerAccNumber)
		->where("\"il\".CustomerNumber = '{$customerAccNumber}' OR \"il\".StatisticsCustomer = '{$customerAccNumber}' OR \"il\".Payer = '{$customerAccNumber}'")
		->group(array('il.ProductGroup', 'il.ProductGroupName', 'ih.Year', 'il.SalesPriceUnitOfMeasure'));
		
		$this->applyDateRangeFilter($select);
		
		$this->applyGroupOrderBy($select);
		
		return $connect->fetchAll($select);
	}
	/**
	 * Return report collection grouped by months, year
	 * @param unknown $customerAccNumber
	 */
	public function getByMonthlyCollection ($customerAccNumber) {
		$connect = Mage::helper('biomssql')->getConnection();
		$select =  $connect->select()
			->from( array( 'ih' => $connect->getTableName('Sales.InvoiceHead')),
					array(
							'Year' => 'ih.Year',
							'Month' => 'ih.InvoiceMonth',
							'InvoiceAmount' => 'SUM(ih.InvoiceAmount)'
					)
			)
			//->join( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceHeadId = ih.InvoiceHeadId', array())
			->joinLeft( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceNumber = ih.InvoiceNumber', array())
			//->where('"il".CustomerNumber = ?', $customerAccNumber)
			->where("\"il\".CustomerNumber = '{$customerAccNumber}' OR \"il\".StatisticsCustomer = '{$customerAccNumber}' OR \"il\".Payer = '{$customerAccNumber}'")
			->group(array('Year', 'ih.InvoiceMonth'));
			
		$this->applyDateRangeFilter($select);
		$this->applyMonthlyOrderBy($select);
		return $connect->fetchAll($select);
	}
	
	public function addNewFieldToArrayBeforePos(array &$array, $position, array $values){
		
		$offset = -1;
		foreach ($array as $key => $value ) {
		
			++$offset;
			if ($key == $position) {
				break;
			}
		}
		$array = array_slice($array, 0, $offset, TRUE) + $values + array_slice($array, $offset, NULL, TRUE);
		
		return $array;
	}
	/**
	 * Add new element in array after given position
	 * @param array $array
	 * @param string $position
	 * @param array $values
	 * @throws Exception
	 * @return array
	 */
	public function addNewFieldToArrayAfterPos(array &$array, $position, array $values){
		
		if (!isset( $array[$position] ) ) {
			throw new Exception(strtr('Array position does not exist (:1)', [':1' => $position]));
		}		
		$offset = 0;
		
		foreach ($array as $key => $value) {
			++$offset;
		
			if ($key == $position) {
				break;
			}
		}
		
		$array = array_slice($array, 0, $offset, TRUE) + $values + array_slice($array, $offset, NULL, TRUE);
		
		return $array;
		
	}
	
	/**
	 * Return report collection details
	 * @param unknown $customerAccNumber
	 * @param string $productGroup
	 */
	public function getInvoicesCollection ($customerAccNumber, $productGroup = null) {
		
		$isPayer = Mage::helper('customeraccount/customer')->getCustomerType($customerAccNumber);
		
		$payerField = array();
		if( 'payer' === strtolower($isPayer) ) {
		//if( 2 === (int) $isPayer ) {
			$payerField  = array (
									//array('before'=>'InvoiceDate', 'data'=>array('Customer Account Number'=>'il.CustomerNumber') ),
									array('after'=>'Mfgr Item#', 'data'=>array(
											'StatisticsCustomer'=> 'il.StatisticsCustomer',
											'StatCustomerName' => 'il.StatCustomerName',
											'Payer'			=> 'il.Payer',
											'PayerName'		=> 'il.PayerName',
									)),
									array('after'=>'Product Group Name', 'data'=>array(
										'DEA Number' => 'ih.FFFDEA',
										'Customer Account Number'=>'il.CustomerNumber',
										'Customer Account Name'=>'il.ShipToName',
										'Shipping To Address1'=>'il.ShipToAddress1',
										'Shipping To Address2'=>'il.ShipToAddress2',
										'Shipping To City'=>'il.ShipToCity',
										'Ship To State'=>'il.ShipToState',
										'Ship To Zip Code' => 'il.ShipToZipCode',
									)),
									array('before'=>'ItemName', 'data'=>array('Manufacturer item number'=>'il.NDCid')),
			);
		}
		
		$col = array (
						'InvoiceDate' => 'ih.InvoiceDate',
						'InvoiceNumber' => 'ih.InvoiceNumber',
						'Customer PO' => 'ih.PurchaseOrderNumber',
						'ItemNumber' => 'il.ItemNumber',
						'ItemName' => 'il.ItemName',
						'Qty' => 'il.InvoicedQuantitySalesPriceUM',
						'UOM' => 'il.SalesPriceUnitOfMeasure',
						'Unit Price' => 'il.SalesPrice',
						'Total Amount' => 'ih.InvoiceAmount',
						'Product Group Name' => 'il.ProductGroupName',
						'Mfgr Item#' => 'il.NDCid'
				);
		
		if( count( $payerField ) > 0 ) {
			
			foreach($payerField as $data){
				
				foreach ($data as $key => $value){
					if( 'before' === $key ){
						$this->addNewFieldToArrayBeforePos( $col, $data[$key], $data['data']);
					}
					if( 'after' === $key){
						$this->addNewFieldToArrayAfterPos( $col, $data[$key], $data['data']);
					}
				}
			}
			
		}
		$connect = Mage::helper('biomssql')->getConnection();
		$select =  $connect->select()
		->from( array( 'ih' => $connect->getTableName('Sales.InvoiceHead')), $col
				/* array(
						'InvoiceDate' => 'ih.InvoiceDate',
						'InvoiceNumber' => 'ih.InvoiceNumber',
						'Customer PO' => 'ih.PurchaseOrderNumber',
						'ItemNumber' => 'il.ItemNumber',
						'ItemName' => 'il.ItemName',
						'Qty' => 'il.InvoicedQuantitySalesPriceUM',
						'UOM' => 'il.SalesPriceUnitOfMeasure',
						'Unit Price' => 'il.SalesPrice',
						'Total Amount' => 'ih.InvoiceAmount',
						'Product Group Name' => 'il.ProductGroupName',
						'Mfgr Item#' => 'il.NDCid'
				) */
		)
		//->join( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceHeadId = ih.InvoiceHeadId', array())
		->joinLeft( array( 'il' => $connect->getTableName('Sales.InvoiceLine') ),'il.InvoiceNumber = ih.InvoiceNumber', array())
		//->where('"il".CustomerNumber = ?', $customerAccNumber); this is test
		->where("\"il\".CustomerNumber = '{$customerAccNumber}' OR \"il\".StatisticsCustomer = '{$customerAccNumber}' OR \"il\".Payer = '{$customerAccNumber}'");
		$this->applyDateRangeFilter($select);
		
		$this->applyDetailViewFilter($select);
		$this->applyDetailOrderBy($select);
		return $connect->fetchAll($select);
	}
	
	protected function applyDetailViewFilter(&$select) {
		$productGroup = $this->getData('productgroup');
		if ( isset($productGroup) && !empty($productGroup) ) {
			$select->where('"il".ProductGroup = ?', $productGroup);
		}
		
		//Year
		$year = $this->getData('year');
		if (isset($year) && !empty($year)) {
			$select->where('ih.Year = ?', $year);
		}
		
		//Month
		$month = $this->getData('month');
		if (isset($month) && !empty($month)) {
			$select->where('ih.InvoiceMonth = ?', $month);
		}
		
		//UOM
		$UOM = $this->getData('UOM');
		if (isset($UOM) && !empty($UOM)) {
			$select->where('il.SalesPriceUnitOfMeasure = ?', $UOM);
		}
	}
	
	protected function applyProductGroupFilter(&$select) {
		$productGroup = $this->getData('productgroup');
		if ( isset($productGroup) && !empty($productGroup) ) {
			$select->where('"il".ProductGroup = ?', $productGroup);
		}
	}
	protected function applyMonthlyOrderBy(&$select) {
		$select->order('ih.Year DESC');
		$select->order('ih.InvoiceMonth DESC');
        $select->order('InvoiceAmount DESC');
	}
	protected function applyGroupOrderBy(&$select) {
		$select->order('ih.Year DESC');
		$select->order('Qty DESC');
		$select->order('UOM DESC');
		$select->order('InvoiceAmount DESC');
	}
	protected function applyDetailOrderBy(&$select) {
		$select->order('ih.InvoiceDate DESC');
		$select->order('Qty DESC');
        $select->order('Total Amount DESC');
	}
	
	protected function isSchedulerReport() {
		if (!is_null($this->getData('report_export')) && $this->getData('report_export') == 'scheduler') {
			return true;
		}
	}
}
