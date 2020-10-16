<?php


namespace SM\Blog\Observer;

use Magento\Framework\Data\Tree\Node as TreeNode;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use SM\Blog\Model\Config;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollectionFactory;

class TopmemuObserver implements ObserverInterface
{

    public function execute(EventObserver $observer)
    {
        // TODO: Implement execute() method.
    }
}
