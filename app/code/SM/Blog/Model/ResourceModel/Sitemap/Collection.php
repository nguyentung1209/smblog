<?php


namespace SM\Blog\Model\ResourceModel\Sitemap;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(){
        $this->_init('SM\Blog\Model\Sitemap', 'SM\Blog\Model\ResourceModel\Sitemap');
    }
}
