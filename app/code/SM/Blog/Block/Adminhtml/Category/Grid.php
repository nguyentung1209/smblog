<?php


namespace SM\Blog\Block\Adminhtml\Category;

use Exception;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollectionFactory;

class Grid extends ExtendedGrid
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    public function __construct(
        CategoryCollectionFactory $blogCollectionFactory,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $blogCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('blog_category_grid');
        $this->setDefaultSort('blog_category_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->categoryCollectionFactory->create();

        $collection = $collection->addFieldToSelect('*');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Sort category tree
     * {@inheritdoc}
     * @throws Exception
     */
    protected function _afterLoadCollection()
    {
        $categories = $this->categoryCollectionFactory->create()
            ->addFieldToSelect('name')
            ->toOptionArray();

        $collection = clone $this->getCollection();
        $ordered    = $this->getCollection()->removeAllItems();
        foreach ($categories as $category) {
            if ($item = $collection->getItemById($category['value'])) {
                $ordered->addItem($item);
            }
        }

        $this->setCollection($collection);

        return parent::_afterLoadCollection();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn('name', [
            'header'   => __('Index Name'),
            'index'    => 'name',
            'filter'   => false,
            'sortable' => false,
            'renderer' => 'SM\Blog\Block\Adminhtml\Category\Grid\Renderer\Title',
        ]);
        $this->addColumn('level', [
            'header'   => __('Level'),
            'index'    => 'level',
            'filter'   => false,
            'sortable' => false
        ]);
        $this->addColumn('path', [
            'header'   => __('Path'),
            'index'    => 'path',
            'filter'   => false,
            'sortable' => false
        ]);
        return parent::_prepareColumns();
    }
}
