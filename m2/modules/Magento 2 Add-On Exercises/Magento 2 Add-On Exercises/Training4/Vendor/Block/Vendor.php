<?php
namespace Training4\Vendor\Block;

use Training4\Vendor\Model\Resource\VendorPosts\Collection as PostCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class Vendor extends \Magento\Framework\View\Element\Template
{
 protected $_vendorCollectionFactory;
 
 protected $_vendorproductCollectionFactory;
 
 protected $vendors;
 
  protected $_productHelper;
 
 protected $vendorsproducts;
 
 protected $vendorsNames;
 
 public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,       
	    \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
		\Magento\Catalog\Helper\Product $productHelper,
	    \Training4\Vendor\Model\Resource\VendorproductPosts\CollectionFactory $vendorproductCollectionFactory,
		\Training4\Vendor\Model\Resource\VendorPosts\CollectionFactory $vendorCollectionFactory,
		 
        array $data = []
    ) {
		  $this->_jsonEncoder = $jsonEncoder;
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
		$this->_productHelper = $productHelper;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
         $this->_vendorproductCollectionFactory = $vendorproductCollectionFactory;
        
        parent::__construct($context, $data);
    }
	
	  public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = $this->_coreRegistry->registry('product');
        }
        return $product;
    }

	
	
   protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Vendor Details'));
    }
	
	
	  public function getProductvendorlist()
	{
	
		$product = $this->getProduct();
		
		$this->vendorsproducts = $this->_vendorproductCollectionFactory->create()
		->addFieldToSelect('*')
		->addFieldToFilter('product_id', ['eq' => $product->getId()]);
		
		$NAMeStrng = '';
		foreach($this->vendorsproducts as $KK => $VV){
			$this->vendors  = $this->_vendorCollectionFactory->create()
			->addFieldToSelect('*')
			->addFieldToFilter('vendor_id', ['eq' => $VV['vendor_id']]);
			
			foreach($this->vendors as $KKV => $VV_V){
				 $NAMeStrng .= $VV_V['name'] .' ,';
			}
			
		}
		
		echo substr($NAMeStrng, 0, -1); 	
       // return $this->vendorsNames;
    }
	
	

}
