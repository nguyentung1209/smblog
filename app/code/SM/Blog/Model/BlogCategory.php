<?php


namespace SM\Blog\Model;


class BlogCategory extends \Magento\Framework\Model\AbstractModel
{
    const BLOG_ROOT_CATEGORY_ID = 1;

    protected function _construct()
    {
        $this->_init('SM\Blog\Model\ResourceModel\BlogCategory');
    }
}
