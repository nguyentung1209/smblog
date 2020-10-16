<?php


namespace SM\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Blog\Api\Data\SitemapInterface;

interface SitemapRepositoryInterface
{

    public function getById($id);

    public function save(SitemapInterface $sitemap);

    public function delete(SitemapInterface $sitemap);

    public function getList(SearchCriteriaInterface $searchCriteria);
}
