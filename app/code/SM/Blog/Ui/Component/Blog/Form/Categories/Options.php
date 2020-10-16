<?php

namespace SM\Blog\Ui\Component\Blog\Form\Categories;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use SM\Blog\Model\BlogCategory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $categoriesTree;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RequestInterface $request
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree()
    {
        if ($this->categoriesTree === null) {
//            $storeId = $this->request->getParam('store');
            /* @var $matchingNamesCollection \SM\Blog\Model\ResourceModel\BlogCategory\Collection */
            $matchingNamesCollection = $this->categoryCollectionFactory->create();

            $matchingNamesCollection->addFieldToSelect('path')
                ->addFieldToFilter('blog_category_id', ['neq' => BlogCategory::BLOG_ROOT_CATEGORY_ID]);

            $shownCategoriesIds = [];

            /** @var \SM\Blog\Model\BlogCategory $category */
            foreach ($matchingNamesCollection as $category) {
                foreach (explode('/', $category->getPath()) as $parentId) {
                    $shownCategoriesIds[$parentId] = BlogCategory::BLOG_ROOT_CATEGORY_ID;
                }
            }

            /* @var $collection \SM\Blog\Model\ResourceModel\BlogCategory\Collection */
            $collection = $this->categoryCollectionFactory->create();

            $collection->addFieldToFilter('blog_category_id', ['in' => array_keys($shownCategoriesIds)])
                ->addFieldToSelect(['name', 'parent_id']);

            $categoryById = [
                BlogCategory::BLOG_ROOT_CATEGORY_ID => [
                    'value' => BlogCategory::BLOG_ROOT_CATEGORY_ID
                ],
            ];

            foreach ($collection as $category) {
                foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => $categoryId];
                    }
                }
                $categoryById[$category->getId()]['label'] = $category->getName();
                $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
            }

            $this->categoriesTree = $categoryById[BlogCategory::BLOG_ROOT_CATEGORY_ID]['optgroup'];
        }

        return $this->categoriesTree;
    }
}
