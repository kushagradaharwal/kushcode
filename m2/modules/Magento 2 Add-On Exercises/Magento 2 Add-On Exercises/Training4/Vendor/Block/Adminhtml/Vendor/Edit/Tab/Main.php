<?php

namespace Training4\Vendor\Block\Adminhtml\Vendor\Edit\Tab;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \SR\Weblog\Model\Status
     */
    protected $_status;

	protected $_vendorproductCollectionFactory;
	
	protected $_vendorCollectionFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Training4\Vendor\Model\Status $status,
		\Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
		  \Training4\Vendor\Model\Resource\VendorproductPosts\CollectionFactory $_vendorproductCollectionFactory,
		    \Training4\Vendor\Model\Resource\VendorPosts\CollectionFactory $_vendorCollectionFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
		 $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_systemStore = $systemStore;
        $this->_status = $status;
		 $this->_vendorCollectionFactory = $_vendorCollectionFactory;
		  $this->_vendorproductCollectionFactory = $_vendorproductCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \SR\Weblog\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('vendor_post');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Vendor Information')]);

        if ($model->getId()) {
            $fieldset->addField('vendor_id', 'hidden', ['name' => 'vendor_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Vendor Name'),
                'title' => __('Vendor Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
	$fieldset->addField(
            'assign_products',
            'multiselect',
            [
                'name' => 'assign_products',
                'label' => __('Assign Products'),
                'title' => __('Assign Products'),
                'values' => $this->getProductList(),
				
            ]
        );
       //$wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
		
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $this->_status->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
	 if ($model->getId()) {
			
			$arrayIds = array();
			$is_active ='';
			$name = '';
			
			$_vendorproductCollection = $this->_vendorproductCollectionFactory->create()->addFieldToSelect('*');	
			$_vendorproductCollection->addFieldToFilter('vendor_id', ['eq' => $model->getId()]);
			foreach($_vendorproductCollection as $itemData){
				$arrayIds[] = $itemData['product_id'];
			}
			
			
			
			$_vendorCollection = $this->_vendorCollectionFactory->create()->addFieldToSelect('*');	
			$_vendorCollection->addFieldToFilter('vendor_id', ['eq' => $model->getId()]);
			foreach($_vendorCollection as $_Data){
				$name = $_Data['name'];
				$is_active = $_Data['is_active'];
			}
			
			$form->setValues(array('vendor_id'=>$model->getId(),'assign_products'=>$arrayIds,'name'=>$name,'is_active'=>$is_active));
			
		}
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Vendor Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Vendor Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
	 protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
	 
	 
	public function getProductList(){
			$planYearArray = array();

			$store = $this->_getStore();
			$collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
				'sku'
			)->addAttributeToSelect(
				'name'
			)->setStore(
				$store
			);
				//$collection->printLogQuery(true); exit;
			foreach ($collection as $collectionK => $collectionV) {
				array_push($planYearArray, array('label' => $collectionV['sku'], 'value' => $collectionV['entity_id']));
			}
			return $planYearArray;
		}
}
