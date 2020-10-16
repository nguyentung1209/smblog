<?php


namespace SM\Blog\Model\ResourceModel\BlogRelatedProductBlog;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogRelatedProductBlog', 'SM\Blog\Model\ResourceModel\BlogRelatedProductBlog');
    }
}
