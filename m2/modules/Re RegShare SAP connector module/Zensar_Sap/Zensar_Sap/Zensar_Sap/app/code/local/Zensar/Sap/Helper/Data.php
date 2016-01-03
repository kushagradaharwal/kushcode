<?php
/* 
 * Helper class of the SAP module
 * @category Zensar
 * @module Zensar_Sap 
 * @author zensar team
 * @created at 17-Nov-2015 11:43 PM IST
 * @modified at 17-Nov-2015 11:43 PM IST
 */
class Zensar_Sap_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isModuleEnbaled(){
        if(Mage::getStoreConfig('sap_settings/sap_connection_details/enabled')) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getSoapUrl(){
       if(Mage::getStoreConfig('sap_settings/sap_connection_details/connect_url')) {
            return Mage::getStoreConfig('sap_settings/sap_connection_details/connect_url');
        } else {
            return false;
        } 
    }
    
    public function getSoapUser(){
       if(Mage::getStoreConfig('sap_settings/sap_connection_details/api_key')) {
            return Mage::getStoreConfig('sap_settings/sap_connection_details/api_key');
        } else {
            return false;
        } 
    }

    public function getSoapPassword(){
       if(Mage::getStoreConfig('sap_settings/sap_connection_details/api_password')) {
            return Mage::getStoreConfig('sap_settings/sap_connection_details/api_password');
        } else {
            return false;
        } 
    }    
}