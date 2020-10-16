<?php

namespace SM\Blog\Model\Blog;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\Blog\Model\Config;
use SM\Blog\Model\BlogGalleryFactory;
use SM\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogCategoryCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedGalleryBlog\CollectionFactory as BlogGalleryCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedProductBlog\CollectionFactory as BlogProductCollectionFactory;
use SM\Blog\Model\ResourceModel\BlogRelatedTagsBlog\CollectionFactory as BlogTagsCollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    protected $productRelatedCollection;
    protected $categoryRelatedCollection;
    protected $tagsRelatedCollection;
    protected $productCollectionFactory;
    protected $dataPersistor;
    protected $loadedData;
    protected $config;
    protected $status;
    protected $imageHelper;
    protected $_productRepository;
    protected $_categoryRepository;
    protected $blogGalleryCollection;
    protected $galleryFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        BlogCollectionFactory $blogCollectionFactory,
        BlogProductCollectionFactory $blogProductCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryRepository,
        BlogCategoryCollectionFactory $blogCategoryCollectionFactory,
        BlogTagsCollectionFactory $blogTagsCollectionFactory,
        BlogGalleryCollectionFactory $blogGalleryCollection,
        BlogGalleryFactory $galleryFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Config $config,
        Status $status,
        ImageHelper $imageHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $blogCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
        $this->config = $config;
        $this->status = $status;
        $this->imageHelper = $imageHelper;
        $this->productRelatedCollection = $blogProductCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_categoryRepository = $categoryRepository;
        $this->categoryRelatedCollection = $blogCategoryCollectionFactory;
        $this->tagsRelatedCollection = $blogTagsCollectionFactory;
        $this->blogGalleryCollection = $blogGalleryCollection;
        $this->galleryFactory = $galleryFactory;
    }

    /**
     * Prepares Meta
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     */
    public function getData()
    {
        // Get items
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $blog) {
            $this->loadedData[$blog->getId()] = $blog->getData();
            if ($blog->getThumbnail()) {
                $this->loadedData[$blog->getId()]['thumbnail'] = [
                    [
                        'name' => $blog->getThumbnail(),
                        'url'  => $this->config->getMediaUrl($blog->getThumbnail()),
                        'size' => filesize($this->config->getMediaPath($blog->getThumbnail())),
                        'type' => 'image'
                    ],
                ];
            }
            $blogGallery = $this->blogGalleryCollection->create()->addFieldToFilter('id', $blog->getId())->getData();
            for ($i = 0; $i < count($blogGallery); $i++) {
                $modelGallery = $this->galleryFactory->create();
                $gallery = $modelGallery->load($blogGallery[$i]['blog_gallery_id'])->getData()['path'];
                $this->loadedData[$blog->getId()]['gallery'][] =
                    [
                        'name' => $gallery,
                        'url'  => $this->config->getMediaUrl($gallery),
                        'size' => filesize($this->config->getMediaPath($gallery)),
                        'type' => 'image'
                    ];
            }
            foreach ($this->productRelatedCollection->create()->addFieldToFilter('id', $blog->getId())->getData() as $product) {
                $productRelated = $this->getProductById($product['product_id']);
                $this->loadedData[$blog->getId()]['links']['products'][] = [
                    'id'        => $product['product_id'],
                    'name'      => $productRelated->getName(),
                    'status'    => $this->status->getOptionText($productRelated->getStatus()),
                    'thumbnail' => $this->imageHelper->init($productRelated, 'product_listing_thumbnail')->getUrl()
                ];
            }
            $categoryRelated = $this->categoryRelatedCollection->create()->addFieldToFilter('id', $blog->getId())->getData();
            $this->loadedData[$blog->getId()]['data']['parent'] = [];
            for ($i = 0; $i < count($categoryRelated); $i++) {
                array_push($this->loadedData[$blog->getId()]['data']['parent'], $categoryRelated[$i]['blog_category_id']);
            }
            $this->loadedData[$blog->getId()]['tag_ids'] = [];
            $tagsRelated = $this->tagsRelatedCollection->create()->addFieldToFilter('id', $blog->getId())->getData();
            for ($i = 0; $i < count($tagsRelated); $i++) {
                array_push($this->loadedData[$blog->getId()]['tag_ids'], $tagsRelated[$i]['blog_tags_id']);
            }
        }

        $data = $this->dataPersistor->get('blog');
        if (!empty($data)) {
            $blog = $this->collection->getNewEmptyItem();
            $blog->setData($data);
            $this->loadedData[$blog->getId()] = $blog->getData();
            $this->dataPersistor->clear('blog');
        }

        return $this->loadedData;
    }
    protected function getProductById($id)
    {
        try {
            return $this->_productRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
    protected function getCategoryById($id)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $_objectManager->create('Magento\Catalog\Model\Category')->load($id);
    }
}
