<?php

namespace SM\Blog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Blog extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    protected $_productRepository;

    protected function _construct()
    {
        $this->_init('SM\Blog\Model\ResourceModel\Blog');
    }

    public function __construct(Context $context, Registry $registry, \Magento\Catalog\Model\ProductRepository $productRepository)
    {
        $this->_productRepository = $productRepository;
        parent::__construct($context, $registry);
    }
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Publish'), self::STATUS_DISABLED => __('Private')];
    }
    public function getProductIds()
    {
        return $this->getData('product_ids');
    }
    protected function getProductById($id)
    {
        try {
            return $this->_productRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
