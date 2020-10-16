<?php

namespace SM\Blog\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use SM\Blog\Model\ResourceModel\Blog\CollectionFactory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollection;
use SM\Blog\Model\ResourceModel\BlogGallery\CollectionFactory as GalleryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogCategoryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedGalleryBlog\CollectionFactory as BlogGalleryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedProductBlog\CollectionFactory as BlogProductCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedTagsBlog\CollectionFactory as BlogTagsCollection;
use SM\Blog\Model\ResourceModel\BlogTags\CollectionFactory as TagsCollection;

class Detail extends \Magento\Framework\View\Element\Template
{
    protected $blogCollectionFactory;

    protected $resultPageFactory;

    protected $categoryCollection;

    protected $blogCategoryCollection;

    protected $tagsCollection;

    protected $blogTagsCollection;

    protected $blogProductCollection;

    protected $galleryCollection;

    protected $blogGalleryCollection;

    protected $_productRepository;

    protected $loadedData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $blogCollectionFactory,
        CategoryCollection $categoryCollection,
        BlogCategoryCollection $blogCategoryCollection,
        TagsCollection $tagsCollection,
        BlogTagsCollection $blogTagsCollection,
        BlogProductCollection $blogProductCollection,
        GalleryCollection $galleryCollection,
        BlogGalleryCollection $blogGalleryCollection,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->categoryCollection = $categoryCollection;
        $this->blogCategoryCollection = $blogCategoryCollection;
        $this->tagsCollection = $tagsCollection;
        $this->blogTagsCollection = $blogTagsCollection;
        $this->blogProductCollection = $blogProductCollection;
        $this->galleryCollection = $galleryCollection;
        $this->blogGalleryCollection = $blogGalleryCollection;
        $this->resultPageFactory = $resultPageFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }
    public function getBlogDetail()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $blogId = $this->getRequest()->getParam('id');
        if (!$blogId) {
            return false;
        } else {
            $now = new \DateTime();
            $blogCollection = $this->blogCollectionFactory->create()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('id', $blogId)
                ->addFieldToFilter('publish_date_from', ['lteq' => $now->format('Y-m-d H:i:s')])
                ->addFieldToFilter('publish_date_to', ['gteq' => $now->format('Y-m-d H:i:s')])
                ->getData();
            if (!empty($blogCollection)) {
                $this->loadedData = $blogCollection[0];
                // get Category Blog
                $relatedBlogCategoryCollection = $this->blogCategoryCollection->create()
                    ->addFieldToFilter('id', $blogId)
                    ->addFieldToSelect('blog_category_id');
                $categoryIds = [];
                foreach ($relatedBlogCategoryCollection->getData() as $item) {
                    array_push($categoryIds, $item['blog_category_id']);
                }
                $categoryRelatedCollection = $this->categoryCollection->create()
                    ->addFieldToFilter('blog_category_id', ['in' => $categoryIds]);
                $this->loadedData['categories'] = [];
                foreach ($categoryRelatedCollection as $item) {
                    $this->loadedData['categories'][] = [
                        'blog_category_id' => $item['blog_category_id'],
                        'name' => $item['name']
                    ];
                }
                // get Tags Blog
                $relatedTagsBlogCollection = $this->blogTagsCollection->create()
                    ->addFieldToFilter('id', $blogId)
                    ->addFieldToSelect('blog_tags_id');
                $tagIds = [];
                foreach ($relatedTagsBlogCollection->getData() as $item) {
                    array_push($tagIds, $item['blog_tags_id']);
                }
                $tagsRelatedCollection = $this->tagsCollection->create()
                    ->addFieldToFilter('blog_tags_id', ['in' => $tagIds]);
                $this->loadedData['tags'] = [];
                foreach ($tagsRelatedCollection as $item) {
                    $this->loadedData['tags'][] = [
                        'blog_tags_id' => $item['blog_tags_id'],
                        'tags' => $item['tags']
                    ];
                }
                // get Related Product
                $relatedProductBlogCollection = $this->blogProductCollection->create()
                    ->addFieldToFilter('id', $blogId)
                    ->addFieldToSelect('product_id');
                $productIds = [];
                foreach ($relatedProductBlogCollection->getData() as $item) {
                    array_push($productIds, $item['product_id']);
                }
                foreach ($productIds as $item) {
                    $this->loadedData['products'][] = [
                        'id' => $this->getProductById($item)->getId(),
                        'name' => $this->getProductById($item)->getName(),
                        'price' => $this->getProductById($item)->getPrice(),
                        'url_key' => $this->getProductById($item)->getUrlModel()->getUrl($this->_productRepository->getById($item)),
                        'image' => $this->getProductById($item)->getData('image')
                    ];
                }
                // get gallery
                $gallery = $this->blogGalleryCollection->create()
                    ->addFieldToFilter('id', $blogId)
                    ->addFieldToSelect('blog_gallery_id');
                $galleryIds = [];
                foreach ($gallery->getData() as $item) {
                    array_push($galleryIds, $item['blog_gallery_id']);
                }
                $galleryBlog = $this->galleryCollection->create()->addFieldToFilter('blog_gallery_id', ['in' => $galleryIds]);
                $this->loadedData['gallery'] = [];
                foreach ($galleryBlog as $item) {
                    $this->loadedData['gallery'][] = [
                        'path' => $item['path'],
                    ];
                }
            } else {
                $this->loadedData = false;
            }
            return $this->loadedData;
        }
    }
    protected function getProductById($id)
    {
        try {
            return $this->_productRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
