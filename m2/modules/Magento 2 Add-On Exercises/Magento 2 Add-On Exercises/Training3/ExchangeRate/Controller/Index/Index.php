<?php
namespace Training3\ExchangeRate\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**C:\xampp\htdocs\cisco\app\code\Training3\ExchangeRate\Controller\Index\Index.php
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
