<?php


namespace SM\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Sitemap extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sitemap', 'sitemap_id');
    }
}
