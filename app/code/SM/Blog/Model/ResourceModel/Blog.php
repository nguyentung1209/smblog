<?php


namespace SM\Blog\Model\ResourceModel;


class Blog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        // table vs primary key
        $this->_init('blog', 'id');
    }
}
