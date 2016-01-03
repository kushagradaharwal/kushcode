<?php
/* 
 * Helper class of the SAP module
 * @category Zensar
 * @module Zensar_Sap 
 * @author zensar team
 * @created at 27-Nov-2015 15:20 PM IST
 * @modified at 27-Nov-2015 11:43 PM IST
 */
class Zensar_Sap_Model_Sap extends Mage_Core_Model_Abstract
{
    /**
     * Call to SOAP
     */
    public function _sapSoapCall(){
        
      echo   $soapUrl = Mage::helper('sap')->getSoapUrl();
      echo   $username = Mage::helper('sap')->getSoapUser();
      echo  $password = Mage::helper('sap')->getSoapPassword();
       die; 
        if($soapUrl && $username && $password ){
           
            $client = new SoapClient($soapUrl);
            $session = $client->login('rohanpatil', 'abcd1234');

            $result = $client->call($session, 'catalog_product.list');
            //echo $client->__getLastRequest()."<br/>";
            //echo $client->__getLastResponse()."<br/>";
            return $result;
            
        }
        // If you don't need the session anymore
        //$client->endSession($session);
	
	//FOR SAP call use below code
        /*$SOAP_OPTS = array( 'login' => 'user_name',
                    'password' => 'password',
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS);

        $client = new SoapClient('http://magentohost/soap/api/?wsdl',$SOAP_OPTS);
        $params = array(
                'test' => 1234
            );
        try {
            $result = $client->sapfunctioncall($params);
            echo $client->_getLastHeaderRequest()."<br/>";
            echo $client->_getLastHeaderResponse()."<br/>";
            print_r($result);
        } catch (Exception $ex) {
            print_r($ex->getMessage());
        }*/
    }
}