<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Training5\VendorRepository\Api;


/**
 * Defines the service layer  for getting the Vndor informaion from vendor Id.
 * Save new Vendor
 * Get List of All the Vendors
 * Get associated productIds of a vendor
 */
interface VendorInterface
{
    /**
     * Return name of the Vendor
     *
     * @api
	 * @param int $vendorId 
     * @return string name of the Vendor.
     */
    public function loadvendor($vendorId);
    
    /**
     * Return string
     *
     * @api
     * @param string $vendorName 
     * @return string.
     */
    public function save($vendorName);
    
    /**
     * Return array
     *
     * @api
     * @param int $vendorName 
     * @return array.
     */
    public function getList();
    
    /**
     * Return array
     *
     * @api
     * @param int $vendorName 
     * @return array.
     */
    public function getAssociatedProductcIds($vendorName);
}
