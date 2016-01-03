<?php
namespace Training2\Specific404page\Model;
 
class Observer
{
    protected $_response;
	protected $request;
	
	 /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;
	
	
	public function __construct(   
	 \Magento\Framework\App\RequestInterface $request,	
	  \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
	\Magento\Framework\UrlInterface $urlManager
	) {
		$this->_request  = $request;
		$this->resultForwardFactory = $resultForwardFactory;
		$this->_urlManager = $urlManager;
		}
	
    public function Specific404page(\Magento\Framework\Event\Observer $observer)
	{
		
		
    }
}