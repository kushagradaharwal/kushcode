<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Training5\VendorRepository\Model;

use Training5\VendorRepository\Api\VendorInterface;
use Magento\Framework\ObjectManagerInterface;


/**
 * Defines the service layer  for getting the Vendor informaion from vendor Id.
 * Save new Vendor
 * Get List of All the Vendors
 * Get associated productIds of a vendor
 */
class Vendorrepository implements VendorInterface
{
    /**
     * @var VendorModel
     */

    protected $request;
	
	protected $_vendorCollectionFactory;
	
	protected $_vendorproductCollectionFactory;
	
	protected $vendorsNames;
	
	protected $_vendorFactory;
	
	protected $_newvendordeatils;
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
         \Training4\Vendor\Model\Resource\VendorPosts\CollectionFactory $vendorCollectionFactory,
		  \Training4\Vendor\Model\Resource\VendorproductPosts\CollectionFactory $vendorproductCollectionFactory,
		   \Training4\Vendor\Model\VendorPostsFactory $vendorFactory)
    {
        $this->_vendorFactory = $vendorFactory;
		 $this->request = $request;
         $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_vendorproductCollectionFactory = $vendorproductCollectionFactory;
    }
    /**
     * Return name of the Vendor
     *
     * @api
     * @param int $vendorId 
     * @return string name of the Vendor.
     */
    public function loadvendor($vendorId)
    { 
        /**
        * Load Vendor with Vendor Id from table and return vendor name
		*/
	
		$Coll =  $this->_vendorCollectionFactory->create()->addFieldToFilter('vendor_id', ['eq' => $vendorId]);
		
		$Return = '';
		foreach($Coll as $itemData){
			$Status = '';			
			$name = $itemData['name'];
			if($itemData['is_active'] == 1){ 
				$Status = 'Enable'; 
			}else {
				$Status = 'Disable'; 
			}			
		 	$Return = "Name: ".$itemData['name']." , Status: ".$Status;			
		} 		
		return $Return;		
    }
    
    /**
     * Return string
     *
     * @api
     * @param string $vendorName 
     * @return string.
     */
    public function save($vendorName)
    {
		//echo  $vendorName;
	//	exit;
        /**
        * 
        * Save New Vendor in the Vendor Table
        * Defaine Vendor table registry in constructor to load the vendor Model
      */
		
       $this->_newvendordeatils  = $this->_vendorFactory->create()->setData(array('name'=> $vendorName,'is_active'=>0))->save();
	   return 'Vendor Name: '.$this->_newvendordeatils->getName().' , Vendor Id: '.$this->_newvendordeatils->getVendorId();
    }
    
    /**
     * Return array
     *
     * @api
     * @param int $vendorName 
     * @return array.
     */
    public function getList()
	{
		$Coll =  $this->_vendorCollectionFactory->create();
		$MyResult =array();
		foreach($Coll as $data){
			$Status = '';
			if($data['is_active'] == 1){ 
				$Status = 'Enable'; 
			}else {
				$Status = 'Disable'; 
			}
			$MyResult[] = $data['name'].' , '.$Status;
		}
		return $MyResult;
	
	}
    
    /**
     * Return array
     *
     * @api
     * @param int $vendorId 
     * @return array.
     */
    public function getAssociatedProductcIds($vendorId)
	{
		$Coll =  $this->_vendorproductCollectionFactory->create()->addFieldToFilter('vendor_id', ['eq' => $vendorId]);
		$MyResult =array();
		foreach($Coll as $data){
			
			$MyResult[] = 'Product Id: '.$data['product_id'];
		}
		return $MyResult;
	}
    
}