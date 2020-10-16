<?php

namespace SM\Blog\Observer;

use Magento\Framework\Data\Tree\Node as TreeNode;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use SM\Blog\Model\Config;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollectionFactory;

class TopMenuObserver implements ObserverInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config                    $config
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Config $config,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->config                    = $config;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * {@inheritdoc}
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => __('Blog NDT'),
            'id'        => 'blog',
            'url'       => '/blog',
        ];
        $node = new TreeNode($data, 'id', $tree, $menu);
        $menu->addChild($node);
        return $this;
    }
}
