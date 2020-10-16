<?php


namespace SM\Blog\Model\ResourceModel\UrlRewrite;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'url_rewrite_id';

    protected function _construct()
    {
        $this->_init('SM\Blog\Model\UrlRewrite', 'SM\Blog\Model\ResourceModel\UrlRewrite');
    }
}
