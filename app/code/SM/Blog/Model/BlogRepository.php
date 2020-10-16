<?php


namespace SM\Blog\Model;


use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\Blog\Api\Data\BlogInterface;
use SM\Blog\Api\BlogRepositoryInterface;
use SM\Blog\Model\ResourceModel\Blog as ResourceBlog;
use SM\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use SM\Blog\Model\ResourceModel\Blog\Collection;

class BlogRepository implements BlogRepositoryInterface
{
    private $blogFactory;
    private $blogCollectionFactory;
    protected $storeManager;
    protected $resource;

    public function __construct(
        BlogFactory $blogFactory,
        BlogCollectionFactory $blogCollectionFactory,
        ResourceBlog $resource
    )
    {
        $this->blogFactory = $blogFactory;
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->resource = $resource;
    }
    public function getById($id)
    {
        $blog = $this->blogFactory->create();
        $blog->getResource()->load($blog, $id);
        if (! $blog->getId()) {
            throw new NoSuchEntityException(__('Unable to find blog with ID "%1"', $id));
        }
        return $blog;
    }

    public function save(BlogInterface $blog)
    {
//        if (empty($blog->getStoreId())) {
//            $blog->setStoreId($this->storeManager->getStore()->getId());
//        }

        try {
            $this->resource->save($blog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $blog;
    }

    public function delete(BlogInterface $blog)
    {
        try {
            $this->resource->delete($blog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    public function deleteById($blogId)
    {
        try {
            return $this->delete($this->getById($blogId));
        } catch (CouldNotDeleteException $e) {
        } catch (NoSuchEntityException $e) {
        }
    }
}
