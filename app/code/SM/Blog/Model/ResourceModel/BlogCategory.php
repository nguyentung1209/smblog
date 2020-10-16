<?php


namespace SM\Blog\Model\ResourceModel;


class BlogCategory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

        protected function _construct()
    {
        // table vs primary key
        $this->_init('blog_category', 'blog_category_id');
    }
}
