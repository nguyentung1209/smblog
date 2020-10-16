<?php


namespace SM\Blog\Model\ResourceModel\BlogGallery;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\BlogGallery', 'SM\Blog\Model\ResourceModel\BlogGallery');
    }
}
