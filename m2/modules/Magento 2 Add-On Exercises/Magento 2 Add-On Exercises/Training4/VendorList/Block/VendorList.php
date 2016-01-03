<?php
namespace Training4\VendorList\Block;

use Training4\VendorList\Model\Resource\VendorPosts\Collection as PostCollection;
use Magento\Framework\ObjectManagerInterface;

class VendorList extends \Magento\Framework\View\Element\Template
{

 protected $_template = 'vendorlist.phtml';
  
  
 protected $_vendorCollectionFactory;
 
 protected $_vendorproductCollectionFactory;
 
 protected $vendors;
 
 protected $vendorsproducts;
 
 protected $vendorsNames;
 
 public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Training4\Vendor\Model\Resource\VendorproductPosts\CollectionFactory $vendorproductCollectionFactory,
		\Training4\Vendor\Model\Resource\VendorPosts\CollectionFactory $vendorCollectionFactory,
        array $data = []
    ) {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
         $this->_vendorproductCollectionFactory = $vendorproductCollectionFactory;
        
        parent::__construct($context, $data);
    }
	
	
   protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Vendor List'));
    }
	
	
	  public function getVendors()
    {
		$this->vendors  = $this->_vendorCollectionFactory->create()->addFieldToSelect('*');	
				 	
        return $this->vendors;
    }
	
	   public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

	 protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getVendors()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager','vendorlist.vendorlist.pager')
			->setCollection($this->getVendors());
            $this->setChild('pager', $pager);
            $this->getVendors()->load();
        }
        return $this;
    }
	

}
