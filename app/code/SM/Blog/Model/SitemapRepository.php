<?php


namespace SM\Blog\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\Blog\Api\Data\SitemapInterface;
use SM\Blog\Api\SitemapRepositoryInterface;
use SM\Blog\Model\ResourceModel\Sitemap\CollectionFactory as SitemapCollectionFactory;
use SM\Blog\Model\ResourceModel\Sitemap\Collection;

class SitemapRepository implements SitemapRepositoryInterface
{
    private $sitemapFactory;
    private $sitemapCollectionFactory;

    public function __construct(
        SitemapFactory $sitemapFactory,
        SitemapCollectionFactory $sitemapCollectionFactory
    )
    {
        $this->sitemapFactory = $sitemapFactory;
        $this->sitemapCollectionFactory = $sitemapCollectionFactory;
    }
    public function getById($id)
    {
        $sitemap = $this->sitemapFactory->create();
        $sitemap->getResource()->load($sitemap, $id);
        if (! $sitemap->getId()) {
            throw new NoSuchEntityException(__('Unable to find hamburger with ID "%1"', $id));
        }
        return $sitemap;
    }

    public function save(SitemapInterface $sitemap)
    {
        // TODO: Implement save() method.
    }

    public function delete(SitemapInterface $sitemap)
    {
        // TODO: Implement delete() method.
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
//        $collection = $this->sitemapCollectionFactory->create();
//
//        $this->addFiltersToCollection($searchCriteria, $collection);
//        $this->addSortOrdersToCollection($searchCriteria, $collection);
//        $this->addPagingToCollection($searchCriteria, $collection);
//
//        $collection->load();
//
//        return $this->buildSearchResult($searchCriteria, $collection);
    }
}
