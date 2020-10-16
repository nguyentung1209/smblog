<?php


namespace SM\Blog\Model\ResourceModel;


class BlogRelatedTagsBlog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        // table vs primary key
        $this->_init('blog_related_tags_blog', 'related_id');
    }
}
