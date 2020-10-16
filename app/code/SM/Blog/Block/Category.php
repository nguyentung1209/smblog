<?php

namespace SM\Blog\Block;

use SM\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogCategoryCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedTagsBlog\CollectionFactory as BlogTagsCollection;
use SM\Blog\Model\ResourceModel\BlogTags\CollectionFactory as TagsCollection;

class Category extends \Magento\Framework\View\Element\Template
{
    protected $blogCollectionFactory;
    protected $categoryCollectionFactory;
    protected $blogCategoryCollectionFactory;
    protected $tagsCollection;
    protected $blogTagsCollection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        BlogCollectionFactory $blogCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        BlogCategoryCollectionFactory $blogCategoryCollectionFactory,
        TagsCollection $tagsCollection,
        BlogTagsCollection $blogTagsCollection
    ) {
        $this->blogCategoryCollectionFactory = $blogCategoryCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->tagsCollection = $tagsCollection;
        $this->blogTagsCollection = $blogTagsCollection;
        parent::__construct($context);
    }
    public function getBlogCollectionByCategory()
    {
        //get values of current page
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        //get values of current sorter
        $sorter = ($this->getRequest()->getParam('blog_list_dir')) ? $this->getRequest()->getParam('blog_list_dir') : 'asc';
        // get values of current order
        $order = ($this->getRequest()->getParam('blog_list_order')) ? $this->getRequest()->getParam('blog_list_order') : 'create_at';
        // get category id
        $categoryId = $this->getRequest()->getParam('id');
        $relatedBlogCategoryCollection = $this->blogCategoryCollectionFactory->create()
            ->addFieldToFilter('blog_category_id', $categoryId)
            ->addFieldToSelect('id');
        $categoryIds = [];
        foreach ($relatedBlogCategoryCollection->getData() as $item) {
            array_push($categoryIds, $item['id']);
        }
        $now = new \DateTime();
        $blogCollection = $this->blogCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('id', ['in' => $categoryIds])
            ->addFieldToFilter('publish_date_from', ['lteq' => $now->format('Y-m-d H:i:s')])
            ->addFieldToFilter('publish_date_to', ['gteq' => $now->format('Y-m-d H:i:s')]);
        if ($order == 'create_at') {
            if ($sorter == 'asc') {
                $blogCollection->setOrder('id', 'ASC');
            } else {
                $blogCollection->setOrder('id', 'DESC');
            }
        }
        $blogCollection->setPageSize($pageSize);
        $blogCollection->setCurPage($page);
        return $blogCollection;
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Blogs'));

        if ($this->getBlogCollectionByCategory()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'toolbar.blog.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15])->setShowPerPage(true)->setCollection(
                $this->getBlogCollectionByCategory()
            );
            $this->setChild('pager', $pager);
            $this->getBlogCollectionByCategory()->load();
        }
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getCategoryByBlogId($blogId)
    {
        $relatedBlogCategoryCollection = $this->blogCategoryCollectionFactory->create()
            ->addFieldToFilter('id', $blogId)
            ->addFieldToSelect('blog_category_id');
        $categoryIds = [];
        foreach ($relatedBlogCategoryCollection->getData() as $item) {
            array_push($categoryIds, $item['blog_category_id']);
        }
        $categoryRelatedCollection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('blog_category_id', ['in' => $categoryIds]);
        $categories = [];
        foreach ($categoryRelatedCollection as $item) {
            $categories[] = [
                'blog_category_id' => $item['blog_category_id'],
                'name' => $item['name'],
                'url_key' => $item['url_key']
            ];
        }
        return $categories;
    }
    public function getTagsByBlogId($blogId)
    {
        $relatedTagsBlogCollection = $this->blogTagsCollection->create()
            ->addFieldToFilter('id', $blogId)
            ->addFieldToSelect('blog_tags_id');
        $tagIds = [];
        foreach ($relatedTagsBlogCollection->getData() as $item) {
            array_push($tagIds, $item['blog_tags_id']);
        }
        $tagsRelatedCollection = $this->tagsCollection->create()
            ->addFieldToFilter('blog_tags_id', ['in' => $tagIds]);
        $tags = [];
        foreach ($tagsRelatedCollection as $item) {
            $tags[] = [
                'blog_tags_id' => $item['blog_tags_id'],
                'tags' => $item['tags']
            ];
        }
        return $tags;
    }
}
