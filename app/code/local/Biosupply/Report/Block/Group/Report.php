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
class Biosupply_Report_Block_Group_Report  extends Mage_Core_Block_Template {
	protected $_mode = 'group';
	protected $_headerString = '';
	public function _construct() {
		$reQuest = $this->getRequest()->getParams();
		if(isset($reQuest['mode']) ) {
			 //$mode = $this->getRequest()->getParam('mode');
			 $mode = $reQuest['mode'];
			 $mode = trim( preg_replace("/[\/\&%#\$]/", "", $mode ) );
			 switch ($mode) {
			 	case 'monthly':
			 		$this->_mode = 'monthly';
			 		//$this->_headerString = $this->__('Purchase History by Month');
			 		break;
			 	case 'all':
			 		$this->_mode = 'all';
			 		//$this->_headerString = $this->__('Product Purchase History');
			 		break;
			 	default:
			 		$this->_mode = 'group';
			 		//$this->_headerString = $this->__('Purchase History by Product Group');
			 		break;
			 }
		}
	}
	
	public function getLink() {
		return Mage::getUrl('bio_report/report');
	}
	
	public function getFromDate(){
		$date = Mage::helper('bio_report')->getDefaultFromDate();
		if(!empty($date)){
			return "&from_date={$date}";
		}
		 return '';
	}
	
	public function getToDate(){
		$date = Mage::helper('bio_report')->getDefaultToDate();;
		
		if( !empty( $date)){
			return "&to_date={$date}";
		} 
		return '';
		
	}
	
	public function getFullParam() {
		return $this->getFromDate() . $this->getToDate();
	}
	public function getLoadReportUrl() {
		return Mage::getUrl("bio_report/report/loadreport");
	}
	public function getActive($mode) {
		$curMode = $this->getRequest()->getParam('mode');
		//if ((!is_null($curMode) && (trim($mode) === $curMode)) || (is_null($curMode) && 'all' === $mode)) {
		//$mode = Mage::helper('bio_report')->cleanMode($mode);
		if ((!is_null($curMode) && (trim($mode) === $this->_mode ) ) || (is_null($curMode) && 'all' === $mode) || ('all/' === trim($curMode) && 'all' === $mode)) {
			return 'active';
		}
		return null;		
	}
	
	public function getParam($mode) {
		
		$mode = preg_replace("/[\/\&%#\$]/", "", $mode );
		
		if( $this->_mode === $mode) {
			return '#';
		} else {
			return '?mode={$mode}';
		}
	}
}
