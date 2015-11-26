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
class Biosupply_Report_Block_Group_Report_Export  extends Biosupply_Report_Block_Group_Datatable {
	
	protected $_list = null;
	protected $_columns = array();
	protected $_from = null;
	protected $_to = null;
	
	public function __construct() {
		parent::__construct();
		
		$this->getDateTime();
	}
	public function getDateTime(){
		$dateData = $this->getRequest()->getParams();
		
		if(isset($dateData['from_date'])){
			$this->_from = $dateData['from_date'];
		}
		
		if(isset($dateData['to_date'])){
			$this->_to = $dateData['to_date'];
		}
	}
	
	public function getAccountLastName(){		
		if ($this->isSchedulerReport()) {
			return $this->getData('accountLastName');
		} else {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			//$default_customeraccount = Mage::helper('customeraccount/customer')->getDefaultCustomeraccount($customer);
			$address = $customer->getPrimaryShippingAddress();
			return $address->getLastname();
		}
	}
	public function setList($collection) {
		$this->_list = $collection;
	}
	
	public function _prepareCollection(){
		
		$collection = $this->getCollection();
		$this->setList($collection);
	} 
	
	public function _prepareColumns(){
		
		if( isset( $this->_list[0] ) ) {
			
			$columnsData = $this->_list[0];
			
			foreach( $columnsData as $columnsName => $data ) {
			
				$this->_columns[] = $columnsName;
			}
		}
	}
	public function checkDetailReport(){
		$mode = $this->getRequest()->getParam('mode');
		$str = '';
		if($mode === 'DETAIL'){
			$str = $this->__('Detail');
		}
		return $str;
	}
	public function checkHeaderFoDetailReport(){
		
		$params = $this->getRequest()->getParams();
		if (isset($params['productgroup']) && !empty($params['productgroup']) && empty($params['Month'])) {
			return $this->__('Group Report');
		}
			
		if (isset($params['Month']) && !empty($params['Month']) ) {
			return $this->__('Monthly Report');
		}
	}	
	
	public function getHeaderInfo(&$data){
		$mode = '';
		$from = '';
		$to = '';
		if ($this->isSchedulerReport()) {
			$mode = $this->getData('reportmode');
			$reportdate = $this->getData('date');
			$from = $reportdate['from'];
			$to = $reportdate['to'];
		} else {	
			$from = $this->_from;
			$to = $this->_to;
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$detail = $this->checkDetailReport();
			if(strlen($detail) <= 0){
				switch ($this->_mode) {
					case 'MONTHLY':
						$mode = $this->__('Monthly Report');
						break;
					case 'ALL':
						$mode = $this->__('All Report');
						break;
					case 'PRODUCT_GROUP':
						$mode = $this->__('Group Report');
						break;
					default:
						break;
				}
				
			} else {
				$mode = $this->checkHeaderFoDetailReport();
			}			
		}		
		$info = array(
			array('FFFBiovision -- All Purchase History '. $detail .' for '.$mode. ' "'. trim($this->getAccountLastName()).'"'),
			array('   '),
			array(
				'Account: '.$this->getAccountLastName(),
			),
			array(
				'Invoice Period: '. $from .' - ' . $to, 
			),
			array('   '),
		);
		foreach ($info as $k => $value ){
			$data[] = $value;
		}
		return $info;
	}
	/**
     * Retrieve grid as Excel Xml
     *
     * @return unknown
     */
	public function getExcel($filename = '', $productGroup  = null) {
        $this->_prepareCollection();
        $this->_prepareColumns();
        $this->getHeaderInfo($data);
        $data[] = $this->_columns;
        
        foreach( $this->_list as $_index => $_item ){
        	$row = array();
        	foreach( $_item as $value ){
        	    //Prevent missing column
        	    if (!$value || empty($value)) {
        	        $value = ' ';
        	    }
        		$row[] = $value;
        	}
        	$data[] = $row;
        }
        
        $xmlObj = new Varien_Convert_Parser_Xml_Excel();
        $xmlObj->setVar('single_sheet', 'product_report');
        //$xmlObj->setVar('fieldnames', array($data));
        $xmlObj->setData($data);
        $xmlObj->unparse();
        return $xmlObj->getData();
    }
    
	protected function _getCsvHeaders($row)
    {
        $header = array();
        if (isset($row[0]) && !empty($row[0])) {
        	$headers = array_keys($row[0]);
        }
        return $headers;
    }
 
    /**
     * Generates CSV file with product's list according to the collection in the $this->_list
     * @return array
     */
    public function getCsv()
    {
    	//$headerInfo = array();
    	$this->_prepareCollection();
      //  if (!is_null($this->_list)) {
            $items = $this->_list;
           //if (count($items) > 0) {
 
                $io = new Varien_Io_File();
                $path = Mage::getBaseDir('var') . DS . 'export';
                $name = md5(microtime());
                $file = $path . DS . $name . '.csv';
                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $path));
                $io->streamOpen($file, 'w+');
                $io->streamLock(true);
 				$headerInfo = $this->getHeaderInfo($headerInfo);
 				foreach ($headerInfo as $info){
 					$io->streamWriteCsv($info);
 				}
                $io->streamWriteCsv($this->_getCsvHeaders($items));
                foreach ($items as $item) {
                    $io->streamWriteCsv($item);
                }
                return array(
                    'type'  => 'filename',
                    'value' => $file,
                    'rm'    => true // can delete file after use
                );
           // }
     //  }
    }
}
