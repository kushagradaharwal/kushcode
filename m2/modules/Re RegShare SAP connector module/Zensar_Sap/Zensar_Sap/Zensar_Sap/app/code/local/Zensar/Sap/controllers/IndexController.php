<?php
/* 
 * Helper class of the SAP module
 * @category Zensar
 * @module Zensar_Sap 
 * @author zensar team
 * @created at 17-Nov-2015 11:43 PM IST
 * @modified at 17-Nov-2015 11:43 PM IST
 */
class Zensar_Sap_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $isModuleEnabled = Mage::helper('sap')->isModuleEnbaled();
        if($isModuleEnabled) {
            //Call to SOAP client
            $soapClient = Mage::getModel('sap/sap')->_sapSoapCall();
            
            echo "<pre><>";
            print_r($soapClient);
            echo "</pre>";
        } else {
            $url = Mage::getUrl();
            Mage::app()->getFrontController()->getResponse()->setRedirect($url)->sendResponse();
        }
    }
}