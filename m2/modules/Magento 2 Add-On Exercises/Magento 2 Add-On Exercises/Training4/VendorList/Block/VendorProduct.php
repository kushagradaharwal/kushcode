<?php
namespace Training4\VendorList\Block;

use Training4\VendorList\Model\Resource\VendorPosts\Collection as PostCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class VendorProduct extends \Magento\Framework\View\Element\Template
{



protected $_productcollection;

protected $_catalogProductVisibility;

 protected $_vendorCollectionFactory;
 
 protected $_vendorproductCollectionFactory;
 
 protected $vendors;
 
 protected $vendorsproducts;
 
  protected $productName;
  
    protected $_productFactory;
	
 protected $urlHelper;
 
 protected $vendorsNames;
 
 public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Training4\Vendor\Model\Resource\VendorproductPosts\CollectionFactory $vendorproductCollectionFactory,
		\Training4\Vendor\Model\Resource\VendorPosts\CollectionFactory $vendorCollectionFactory,
		 \Magento\Catalog\Model\Resource\Product\CollectionFactory $productcollection,
		 \Magento\Catalog\Model\ProductFactory $productFactory,
		 \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		 \Magento\Framework\Url\Helper\Data $urlHelper,
		 
        array $data = []
    ) {
		$this->_productcollection = $productcollection;
		$this->_productFactory = $productFactory;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
         $this->_vendorproductCollectionFactory = $vendorproductCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
		$this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }
	

	
	
   protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Vendor Product Page'));
    }
	
	
	  public function getProductvendorlist()
    {        
        $this->vendorsproducts = $this->_vendorproductCollectionFactory->create()->addFieldToSelect('*');
			
        return $this->vendorsproducts;
    }
	
	
	   public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

	 protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getProductvendorlist()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager','vendorlist.vendorproduct.pager')
			->setCollection($this->getProductvendorlist());
            $this->setChild('pager', $pager);
            $this->getProductvendorlist()->load();
        }
        return $this;
    }
	
	  public function getVendorName($VendorId)
    {       
		$name = '';
        $_vendorCollection = $this->_vendorCollectionFactory->create()->addFieldToSelect('*');	
			$_vendorCollection->addFieldToFilter('vendor_id', ['eq' => $VendorId]);
			foreach($_vendorCollection as $_Data){
				$name = $_Data['name'];
				
			}
			
		echo $name;
        //return $this->vendorsNames;
    }
	
	  public function getVendorStatus($VendorId)
    {       
		$Status = '';
        $_vendorCollection = $this->_vendorCollectionFactory->create()->addFieldToSelect('*');	
			$_vendorCollection->addFieldToFilter('vendor_id', ['eq' => $VendorId]);
			foreach($_vendorCollection as $_Data){
				$Status = $_Data['is_active'];
				
			}
		if($Status == 1){
			echo "Enable";
		}else if($Status == 2){
			echo "Disable";
		}else{
			echo "NA";
		}	

        //return $this->vendorsNames;
    }
	
	
	
	  public function getProductName($PrdId)
    {   
		$itemDelete =  $this->_productFactory->create()->load($PrdId);
		echo $itemDelete['name'];
	
    }
	
	
	

}
