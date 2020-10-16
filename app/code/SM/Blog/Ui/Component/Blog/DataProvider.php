<?php

namespace SM\Blog\Ui\Component\Blog;

use SM\Blog\Model\ResourceModel\Blog\CollectionFactory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as BlogCategoryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogRelatedCategoryCollection;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $blogCategoryCollection;

    protected $blogRelatedCategoryCollection;

    public function __construct(
        CollectionFactory $collectionFactory,
        BlogCategoryCollection $blogCategoryCollection,
        BlogRelatedCategoryCollection $blogRelatedCategoryCollection,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        $data = []
    ) {
        $this->blogCategoryCollection = $blogCategoryCollection;
        $this->blogRelatedCategoryCollection = $blogRelatedCategoryCollection;
        $this->collection = $collectionFactory->create(); // $this->collection will be mapped with dataprovider and render output. You can adjusting collection in here
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
//    public function getData()
//    {
//        $this->collection = $this->collection->toArray();
//        for ($i = 0; $i < count($this->collection); $i++) {
//            $relatedBlogCategoryCollection = $this->blogRelatedCategoryCollection->create()
//                ->addFieldToFilter('id', $this->collection[$i][0]['id'])
//                ->addFieldToSelect('blog_category_id');
//            $categoryIds = [];
//            foreach ($relatedBlogCategoryCollection->getData() as $datum) {
//                array_push($categoryIds, $datum['blog_category_id']);
//            }
//            $categoryRelatedCollection = $this->blogCategoryCollection->create()
//                ->addFieldToFilter('blog_category_id', ['in' => $categoryIds]);
//            $categories = "";
//            foreach ($categoryRelatedCollection as $category) {
//                $categories .= $category['name'] . " ";
//            }
//            $this->collection[$i][0]['name'][] = 'categories';
//        }
//        return $this->collection;
//    }
}
