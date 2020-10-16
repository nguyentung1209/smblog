<?php


namespace SM\Blog\Api;


use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Blog\Api\Data\BlogInterface;

interface BlogRepositoryInterface
{
    public function getById($id);

    public function save(BlogInterface $blog);

    public function delete(BlogInterface $blog);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function deleteById($blogId);
}
