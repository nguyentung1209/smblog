<?php


namespace SM\Blog\Model\ResourceModel\Blog;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\Blog', 'SM\Blog\Model\ResourceModel\Blog');
    }
}
