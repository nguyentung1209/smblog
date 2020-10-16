<?php


namespace SM\Blog\Model\ResourceModel\BlogRelatedGalleryBlog;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogRelatedGalleryBlog', 'SM\Blog\Model\ResourceModel\BlogRelatedGalleryBlog');
    }
}
