<?php


namespace SM\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Registry;
use SM\Blog\Model\BlogCategoryFactory;

abstract class Category extends Action
{
    /**
     * @var BlogCategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param BlogCategoryFactory $blogCategoryFactory
     * @param Registry        $registry
     * @param Context         $context
     */
    public function __construct(
        BlogCategoryFactory $blogCategoryFactory,
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $blogCategoryFactory;
        $this->registry        = $registry;
        $this->context         = $context;

        parent::__construct($context);
    }

    /**
     * @return \SM\Blog\Model\BlogCategory
     */
    public function initModel()
    {
        $model = $this->categoryFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    protected function initPage($resultPage)
    {
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Blog::blog_category');
    }
}
