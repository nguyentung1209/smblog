<?php


namespace SM\Blog\Model\ResourceModel;


class BlogTags extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        // table vs primary key
        $this->_init('blog_tags', 'blog_tags_id');
    }
}
