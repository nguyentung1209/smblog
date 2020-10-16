<?php


namespace SM\Blog\Model\ResourceModel;


class UrlRewrite extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        // table vs primary key
        $this->_init('url_rewrite', 'url_rewrite_id');
    }
}
