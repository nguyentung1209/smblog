<?php


namespace SM\Blog\Model\ResourceModel;


class BlogRelatedGalleryBlog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        // table vs primary key
        $this->_init('blog_related_gallery_blog', 'related_id');
    }
}
