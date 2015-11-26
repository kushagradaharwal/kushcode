<?php
class Mfv_History_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$customer_session = Mage::helper("customer")->isLoggedIn();
		if($customer_session)
		{
		 $this->loadLayout();     
		 $this->renderLayout();
        }
		else
		{
		$url = Mage::getBaseUrl()."customer/account/login";
		$this->_redirectUrl($url);
		}
	}
	
	public function statusfilterAction()
    {
	 $this->loadLayout();     
		//$this->renderLayout();
	 $val = $this->getRequest()->getParam('ordered'); //status value
 	  
     $order =  $this->getRequest()->getParam('ordered'); //status value
   
     $filter_collection = Mage::getModel("history/history")->getFilter($val);
    
 	 Mage::register('filtercollection', $filter_collection);
    
	 $this->renderLayout();
     }
 	
	
	
	public function seasonfilterAction()
    {
	    $customer_session = Mage::helper("customer")->isLoggedIn();
		if($customer_session)
		{
	  $this->loadLayout();     

	 $year = $this->getRequest()->getParam('year'); //season value
	  
	 $order =  $this->getRequest()->getParam('ordered'); //orderd value
	 
	 $valdir = $this->getRequest()->getParam('dir'); //status dir  value  like asc and desc
		
	$ordercolumn = $this->getRequest()->getParam('order'); //status  order value
	
	 $filter_collection = Mage::getModel("history/history")->getseasonFilter($order, $year ,$valdir ,$ordercolumn);
   // echo '<pre>';print_r( $filter_collection );die;
	
 	 Mage::register('filtercollection', $filter_collection);
    
	 $this->renderLayout();
     }
	 else
		{
		$url = Mage::getBaseUrl()."customer/account/login";
		$this->_redirectUrl($url);
		}
		
	} 
	 
	public function columnfilterAction()
    {
	 
	 $customer_session = Mage::helper("customer")->isLoggedIn();
		if($customer_session)
		{
		
		$this->loadLayout();     
		//$this->renderLayout();
	

	$orderd = $this->getRequest()->getParam('ordered'); //status value
	$year = $this->getRequest()->getParam('year'); //status year value
	$valdir = $this->getRequest()->getParam('dir'); //status dir  value  like asc and desc
	$order = $this->getRequest()->getParam('order'); //status  order value
	
	$filter_collection = Mage::getModel("history/history")->getcolumnFilter($valdir,$order,$orderd,$year);
    
 	 Mage::register('filtercollection', $filter_collection);
    
	 $this->renderLayout();
	 
     }
	 else
		{
		$url = Mage::getBaseUrl()."customer/account/login";
		$this->_redirectUrl($url);
		}
		
	 }
	 
	 
	public function ordertotalseasonAction()
    {
	 $customer_session = Mage::helper("customer")->isLoggedIn();
     if($customer_session)
	  {
		
     $this->loadLayout(); 
	
	 $filter_collection = Mage::getModel("history/history")->getordertotalbyseason();
	 $this->renderLayout();
	 }
	 else
		{
		$url = Mage::getBaseUrl()."customer/account/login";
		$this->_redirectUrl($url);
		}
		
	}
	
	
	 public function downloadhistoryAction() {	
		try{
			if(Mage::getSingleton( 'customer/session' )->isLoggedIn()){
				$request = $this -> getRequest();
				
				$actionType = $request -> getParam('actionType');
				$fileType = $request -> getParam('fileType');
				
				$ordered = $request->getPost('paramcsvordered');  //ordered value
						
				$year = $request->getPost('paramcsvyear'); //Year value
				
				$dir= $request->getPost('paramcsvdir'); //Dir value
				
				$order = $request->getPost('paramcsvorder'); //Order value
				
				//if ($actionType && ($actionType == 'download' || $actionType == 'attachment') ){				
				$fileName = rand();
				$contentType = 'application/pdf';
				if (strtolower($fileType) == 'csv') {
					$contentType = 'text/csv';
					$fileType = 'csv';
					//if (!Mage::helper('customcustomer/pricelist')->getPriceFilePath($default_customeraccount['account_number'], $fileType)) {
						
						if(isset($ordered) || isset($year))
						{
						  Mage::helper('history')->generateBuyingReport($fileType, $fileName ,$ordered,$year,$dir,$order);
						}
						
					//}
				} else {					
					$contentType = 'application/pdf';
					$fileType = 'pdf';
					//if (!Mage::helper('customcustomer/pricelist')->getPriceFilePath($default_customeraccount['account_number'], $fileType)) {					
						Mage::helper('history')->generateBuyingReport($fileType, $fileName);
					//}
				}
				
				$fileUrl = Mage::helper('history')->getDownloadedReportUrl($fileName, $fileType);
			    $baseMediaDir = Mage::getBaseDir('media');
				//$baseMediaUrl = Mage::getBaseUrl('media');
				//$pdfFileUrl = $baseMediaUrl . 'pricelist/' . date('Ymd') . '/' . $customerAccountNo . '.pdf';
				$httpClientConfig = array('maxredirects' => 1);
				$httpClient = new Zend_Http_Client($fileUrl, $httpClientConfig);
				
				//$httpClient->setAdapter('Zend_Http_Client_Adapter_Curl');
				/*$httpBasicAgentPortalArtUser = Mage::getModel('core/variable')->loadByCode('agent_portal_art_u')->getValue('plain');
				$httpBasicAgentPortalArtPasswd = Mage::getModel('core/variable')->loadByCode('agent_portal_art_p')->getValue('plain');
				//'shutterflyusers', 'Shutterfly@2015'
				$httpClient->setAuth($httpBasicAgentPortalArtUser, $httpBasicAgentPortalArtPasswd);*/

				//$httpClient->setStream(); // will use temp file
				//$response = $httpClient->request();
				
				// copy file				
				//copy($response->getStreamName());
			    $filepath = $baseMediaDir . '/buyinghistory/' . date('Ymd') . '/' .Mage::helper('history')->getFileName($fileName, $fileType);

			 
				if ($actionType && ($actionType == 'download' || $actionType == 'attachment') ){
					//$this -> getResponse ()
								//->setHttpResponseCode ( 200 )
								//->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
								//->setHeader ( 'Pragma', 'public', true )
								//->setHeader ( 'Content-type', 'application/force-download' )
								//->setHeader ( 'Content-type', $contentType, true)	
								//->setHeader ('Content-Disposition: inline; filename="' . Mage::helper('customcustomer/pricelist')->getPricePdfFileName($customerAccountNo) . '"');
								//->setHeader('Content-Disposition', 'inline' . '; filename='.$info['title'])									
								//->setHeader('Content-Disposition', 'attachment; filename='.Mage::helper('customcustomer/pricelist')->getPriceFileName($customerAccountNo, $fileType));
								//->setHeader('Content-Disposition', 'attachment; filename="'.Mage::helper('history')->getFileName($fileName, $fileType).'"', true);
								//->header("Content-Disposition: inline; filename=" . $customerAccountNo . "_pricelist_". date(Ymd). ".pdf");
				$this->getResponse ()
						->setHttpResponseCode ( 200 )
						->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
						 ->setHeader ( 'Pragma', 'public', true )
						->setHeader ( 'Content-type', 'application/force-download' )
						->setHeader ( 'Content-Length', filesize($filepath) )
						->setHeader ('Content-Disposition', 'attachment' . '; filename=' . basename($filepath) );
						
				} else {
					$this -> getResponse ()
								->setHttpResponseCode ( 200 )
								->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
								->setHeader ( 'Pragma', 'public', true )
								//->setHeader ( 'Content-type', 'application/force-download' )
								->setHeader ( 'Content-type', $contentType )
								//->setHeader ( 'Content-Length', filesize($pdfFileName) );
								->setHeader ('Content-Disposition', 'form-data');
				}
				$this -> getResponse () -> clearBody ();				
				//$this -> getResponse () -> setBody($response -> getBody());
				$this -> getResponse () -> sendHeaders ();
                readfile($filepath);				
				session_write_close();
				exit;
				
			} else {
				$this->_redirectUrl( Mage::getBaseUrl().'customer/account/login');
			}
		}catch(Exception $error) {
			//Mage::log("");
		}
	 }
	
	public function ordertotalseasonpdfAction() 
	{

			
	  	$this->loadLayout();     
		$this->renderLayout();
	
	}

 
	/*&public function downloadhistorySeasonAction() {		
		try{
			if(Mage::getSingleton( 'customer/session' )->isLoggedIn()){
				$request = $this -> getRequest();
				
				$actionType = $request -> getParam('actionType');
				$fileType = $request -> getParam('fileType');
				//if ($actionType && ($actionType == 'download' || $actionType == 'attachment') ){				
				$fileName = rand();
				$contentType = 'application/pdf';
				if (strtolower($fileType) == 'csv') {
					$contentType = 'text/csv';
					$fileType = 'csv';
					//if (!Mage::helper('customcustomer/pricelist')->getPriceFilePath($default_customeraccount['account_number'], $fileType)) {
						Mage::helper('history')->generateBuyingReportSeason($fileType, $fileName);
					//}
				} else {					
					$contentType = 'application/pdf';
					$fileType = 'pdf';
					//if (!Mage::helper('customcustomer/pricelist')->getPriceFilePath($default_customeraccount['account_number'], $fileType)) {					
						Mage::helper('history')->generateBuyingReport($fileType, $fileName);
					//}
				}
				
				$fileUrl = Mage::helper('history')->getDownloadedReportUrl($fileName, $fileType);
				//$baseMediaUrl = Mage::getBaseUrl('media');
				//$pdfFileUrl = $baseMediaUrl . 'pricelist/' . date('Ymd') . '/' . $customerAccountNo . '.pdf';
				$httpClientConfig = array('maxredirects' => 1);
				$httpClient = new Zend_Http_Client($fileUrl, $httpClientConfig);
				
				$httpClient->setAdapter('Zend_Http_Client_Adapter_Curl');
				/*$httpBasicAgentPortalArtUser = Mage::getModel('core/variable')->loadByCode('agent_portal_art_u')->getValue('plain');
				$httpBasicAgentPortalArtPasswd = Mage::getModel('core/variable')->loadByCode('agent_portal_art_p')->getValue('plain');
				//'shutterflyusers', 'Shutterfly@2015'
				$httpClient->setAuth($httpBasicAgentPortalArtUser, $httpBasicAgentPortalArtPasswd);*/

				/*$httpClient->setStream(); // will use temp file
				$response = $httpClient->request();
				// copy file				
				copy($response->getStreamName(), "");
				
				if ($actionType && ($actionType == 'download' || $actionType == 'attachment') ){
					$this -> getResponse ()
								->setHttpResponseCode ( 200 )
								->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
								->setHeader ( 'Pragma', 'public', true )
								//->setHeader ( 'Content-type', 'application/force-download' )
								->setHeader ( 'Content-type', $contentType, true)	
								//->setHeader ('Content-Disposition: inline; filename="' . Mage::helper('customcustomer/pricelist')->getPricePdfFileName($customerAccountNo) . '"');
								//->setHeader('Content-Disposition', 'inline' . '; filename='.$info['title'])									
								//->setHeader('Content-Disposition', 'attachment; filename='.Mage::helper('customcustomer/pricelist')->getPriceFileName($customerAccountNo, $fileType));
								->setHeader('Content-Disposition', 'attachment; filename="'.Mage::helper('history')->getFileName($fileName, $fileType).'"', true);
								//->header("Content-Disposition: inline; filename=" . $customerAccountNo . "_pricelist_". date(Ymd). ".pdf");
				} else {
					$this -> getResponse ()
								->setHttpResponseCode ( 200 )
								->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
								->setHeader ( 'Pragma', 'public', true )
								//->setHeader ( 'Content-type', 'application/force-download' )
								->setHeader ( 'Content-type', $contentType )
								//->setHeader ( 'Content-Length', filesize($pdfFileName) );
								->setHeader ('Content-Disposition', 'form-data');
				}
				$this -> getResponse () -> clearBody ();				
				$this -> getResponse () -> setBody($response -> getBody());
				$this -> getResponse () -> sendHeaders ();   
				session_write_close();
				
			} else {
				$this->_redirectUrl( Mage::getBaseUrl().'customer/account/login');
			}
		}catch(Exception $error) {
			//Mage::log("");
		}
	 }
	*/ 
}