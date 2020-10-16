<?php


namespace SM\Blog\Model\ResourceModel\BlogTags;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'blog_tags_id';

    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogTags', 'SM\Blog\Model\ResourceModel\BlogTags');
    }
}
