<?php

namespace SM\Blog\Block;

use SM\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedTagsBlog\CollectionFactory as BlogTagsCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogTags\CollectionFactory as TagsCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogCategoryCollection;

class Tag extends \Magento\Framework\View\Element\Template
{
    protected $blogCollectionFactory;
    protected $tagsCollectionFactory;
    protected $blogTagsCollectionFactory;
    protected $blogCategoryCollection;
    protected $categoryCollection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        BlogCollectionFactory $blogCollectionFactory,
        TagsCollectionFactory $tagsCollectionFactory,
        BlogTagsCollectionFactory $blogTagsCollectionFactory,
        CategoryCollection $categoryCollection,
        BlogCategoryCollection $blogCategoryCollection
    ) {
        $this->tagsCollectionFactory = $tagsCollectionFactory;
        $this->blogTagsCollectionFactory = $blogTagsCollectionFactory;
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->blogCategoryCollection = $blogCategoryCollection;
        $this->categoryCollection = $categoryCollection;
        parent::__construct($context);
    }
    public function getBlogCollectionByTags()
    {
        //get values of current page
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        //get values of current sorter
        $sorter = ($this->getRequest()->getParam('blog_list_dir')) ? $this->getRequest()->getParam('blog_list_dir') : 'asc';
        // get values of current order
        $order = ($this->getRequest()->getParam('blog_list_order')) ? $this->getRequest()->getParam('blog_list_order') : 'create_at';
        $blogCollection = [];
        $tagId = $this->getRequest()->getParam('id');
        $relatedBlogTagsCollection = $this->blogTagsCollectionFactory->create()
            ->addFieldToFilter('blog_tags_id', $tagId)
            ->addFieldToSelect('id');
        $blogIds = [];
        foreach ($relatedBlogTagsCollection->getData() as $item) {
            array_push($blogIds, $item['id']);
        }
        $now = new \DateTime();
        $blogCollection = $this->blogCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('id', ['in' => $blogIds])
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

        if ($this->getBlogCollectionByTags()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'toolbar.blog.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15])->setShowPerPage(true)->setCollection(
                $this->getBlogCollectionByTags()
            );
            $this->setChild('pager', $pager);
            $this->getBlogCollectionByTags()->load();
        }
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getCategoryByBlogId($blogId)
    {
        $relatedBlogCategoryCollection = $this->blogCategoryCollection->create()
            ->addFieldToFilter('id', $blogId)
            ->addFieldToSelect('blog_category_id');
        $categoryIds = [];
        foreach ($relatedBlogCategoryCollection->getData() as $item) {
            array_push($categoryIds, $item['blog_category_id']);
        }
        $categoryRelatedCollection = $this->categoryCollection->create()
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
        $relatedTagsBlogCollection = $this->blogTagsCollectionFactory->create()
            ->addFieldToFilter('id', $blogId)
            ->addFieldToSelect('blog_tags_id');
        $tagIds = [];
        foreach ($relatedTagsBlogCollection->getData() as $item) {
            array_push($tagIds, $item['blog_tags_id']);
        }
        $tagsRelatedCollection = $this->tagsCollectionFactory->create()
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
