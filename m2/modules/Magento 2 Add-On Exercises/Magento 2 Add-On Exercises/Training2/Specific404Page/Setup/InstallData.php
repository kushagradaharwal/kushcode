<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Training2\Specific404page\Setup;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsPages = [
            [
                'title' => 'Category Not Found',
                'page_layout' => '2columns-right',
                'meta_keywords' => 'Page keywords',
                'meta_description' => 'Page description',
                'identifier' => 'category-not-found',
                'content_heading' => 'Category Not Found',
                'content' => "<div class=\"page-title\"><h1>404 Page , Category not existing.</h1></div>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ],
            [
                'title' => 'Product Not Found',
                'page_layout' => '2columns-right',
                'identifier' => 'prouduct-not-found',
                'content_heading' => 'Product Not Found',
                'content' => "<div class=\"page-title\"><h2>404 Page , Product not existing.</h2></div>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ],
            [
                'title' => 'Another Page',
                'page_layout' => '2columns-right',
                'identifier' => 'another-page',
                'content_heading' => 'Another 404 Page',
                'content' => "<div class=\"page-title\"><h1>404 Page , Page or data is not available.</h1></div>",
                'is_active' => 1,
                'stores' => [0]
            ]
        ];

        /**
         * Insert default and system pages
         */
        foreach ($cmsPages as $data) {
            $this->createPage()->setData($data)->save();
        }
    }

    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }
}
