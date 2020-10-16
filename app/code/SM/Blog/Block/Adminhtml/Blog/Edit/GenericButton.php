<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\Blog\Block\Adminhtml\Blog\Edit;

use Magento\Backend\Block\Widget\Context;
use SM\Blog\Model\BlogRepository;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BlogRepository
     */
    protected $blogRepository;

    /**
     * @param Context $context
     * @param BlogRepository $blogRepository
     */
    public function __construct(
        Context $context,
        BlogRepository $blogRepository
    ) {
        $this->context = $context;
        $this->blogRepository = $blogRepository;
    }

    /**
     * Return CMS blog ID
     *
     * @return int|null
     */
    public function getBlogId()
    {
        try {
            return $this->blogRepository->getById(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
    public function canRender($name)
    {
        return $name;
    }
}
