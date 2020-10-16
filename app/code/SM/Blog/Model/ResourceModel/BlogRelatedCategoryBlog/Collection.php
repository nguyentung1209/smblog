<?php


namespace SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogRelatedCategoryBlog', 'SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog');
    }
}
