<?php

namespace SM\Blog\Model\BlogCategory;

use Magento\Framework\App\Request\DataPersistorInterface;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $blogCategoryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $blogCategoryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
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
            $this->loadedData[$blog->getBlogCategoryId()] = $blog->getData();
            $this->loadedData[$blog->getBlogCategoryId()]['data']['parent'] = [];
            array_push($this->loadedData[$blog->getBlogCategoryId()]['data']['parent'], $blog->getParentId());
        }
        $data = $this->dataPersistor->get('blog_category');
        if (!empty($data)) {
            $blog = $this->collection->getNewEmptyItem();
            $blog->setData($data);
            $this->loadedData[$blog->getBlogCategoryId()] = $blog->getData();
            $this->dataPersistor->clear('blog_category');
        }
        return $this->loadedData;
    }
}
