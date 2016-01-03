<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Training1\Freegeoip\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getVisitorCountry()
    {
        // Get Visitors remote IP Address
      //  $testIPAddress = Mage::getStoreConfig('visitors_country_section/visitors_country_group/test_ip');
        /**
         * This condition added for testing purpose as API not working for local ip address 
         * User $visitorRemoteAddr = Mage::helper('core/http')->getRemoteAddr() for production
         */
      //  if(!$testIPAddress) {
      //      $visitorRemoteAddr = Mage::helper('core/http')->getRemoteAddr();
      //  } else {
      //      $visitorRemoteAddr = $testIPAddress;
      //  }
       // $location = Mage::getStoreConfig('visitors_country_section/visitors_country_group/api_url');
        $getJsonData = file_get_contents('https://freegeoip.net/json/');
        /*
         * Start of commented Code Added for testing purpose as file_get_content was not working on local due to bandwidth issue.
         */
        //$getJsonData = '{"ip":"183.78.187.83","country_code":"IN","country_name":"India","region_code":"RJ","region_name":"Rajasthan","city":"Nagar","zip_code":"144410","time_zone":"Asia/Kolkata","latitude":27.433,"longitude":77.1,"metro_code":0}';
        /**
         * End of commented code
         */
       $jsonDecode = Mage::helper('core')->jsonDecode($getJsonData);
	  
        return $jsonDecode;
    }
}
