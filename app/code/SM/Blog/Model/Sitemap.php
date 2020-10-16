<?php


namespace SM\Blog\Model;

use Magento\Framework\Model\AbstractModel;


class Sitemap extends  AbstractModel
{
    protected function _construct()
    {
        $this->_init('SM\Blog\Model\ResourceModel\Sitemap');
    }
}
