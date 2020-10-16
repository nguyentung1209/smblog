<?php

namespace SM\Blog\Controller\Index;

use Magento\Framework\App\Action\Context;
use SM\Blog\Model\SitemapFactory;
use SM\Blog\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Framework\App\Action\Action;
use SM\Blog\Model\SitemapRepository;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $sitemap;
    protected $sitemapCollectionFactory;
    protected $_sitemapRepository;
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param SitemapFactory $sitemap
     * @param CollectionFactory $sitemapCollectionFactory
     * @param SitemapRepository $sitemapRepository
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(SitemapFactory $sitemap, CollectionFactory $sitemapCollectionFactory, SitemapRepository $sitemapRepository, Context $context, PageFactory $resultPageFactory)
    {
        $this->sitemapCollectionFactory = $sitemapCollectionFactory;
        $this->sitemap = $sitemap;
        $this->_sitemapRepository = $sitemapRepository;
        $this->resultPageFactory = $resultPageFactory;
        return parent::__construct($context);
    }

    /**
     * Function execute
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
//        $sitemap = $this->sitemap->create()->load(1)
//        ->setData('sitemap_type', 'tung')
//        ->setData('sitemap_filename', 'tungnguyen')
//        ->setData('sitemap_path', 'tungnguyenduy');
//        $sitemap->save();
//        var_dump($sitemap->getData());
//        $sitemap = $this->sitemap->create()
//
//            ->setData('sitemap_type', "1")
//
//            ->setData('sitemap_filename', 'city 1')
//
//            ->setData('sitemap_path', 'project 1');
//
//        $sitemap->save();
//        $sitemapCollectionFactory = $this->sitemapCollectionFactory->create()
//            ->addFieldToFilter('sitemap_type', 'tung')
//            ->addFieldToSelect('sitemap_filename');
//        var_dump($sitemapCollectionFactory->getData());
//            $sitemapId = 1;//any id
//           var_dump($this->_sitemapRepository->getById($sitemapId));
    }
}
