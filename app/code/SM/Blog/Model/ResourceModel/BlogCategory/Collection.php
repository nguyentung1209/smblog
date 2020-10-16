<?php

namespace SM\Blog\Model\ResourceModel\BlogCategory;

use Magento\Framework\App\ObjectManager;
use SM\Blog\Helper\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'blog_category_id';

    protected $fromRoot = true;
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogCategory', 'SM\Blog\Model\ResourceModel\BlogCategory');
    }
    public function getRootId()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var Category $helper */
        $helper = $objectManager->get('\SM\Blog\Helper\Category');

        return $helper->getRootCategory()->getData()[0]['blog_category_id'];
    }

    /**
     * @param int|null $parentId
     *
     * @return \SM\Blog\Model\BlogCategory[]
     */
    public function getTree($parentId = null)
    {
        $list = [];

        if ($parentId == null) {
            $parentId = $this->fromRoot ? 0 : $this->getRootId();
        }

        $collection = clone $this;
        $collection->addFieldToFilter('parent_id', $parentId)
            ->setOrder('blog_category_id', 'asc');
;

        foreach ($collection as $item) {
            $list[$item->getId()] = $item;
            if ($item->getChildrenCount()) {
                $items = $this->getTree($item->getId());
                foreach ($items as $child) {
                    $list[$child->getId()] = $child;
                }
            }
        }

        return $list;
    }
}
