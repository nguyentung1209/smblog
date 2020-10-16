<?php


namespace SM\Blog\Model\ResourceModel;


class BlogGallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        // table vs primary key
        $this->_init('blog_gallery', 'blog_gallery_id');
    }
}
