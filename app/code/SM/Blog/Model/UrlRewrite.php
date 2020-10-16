<?php


namespace SM\Blog\Model;


class UrlRewrite extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\ResourceModel\UrlRewrite');
    }
}
