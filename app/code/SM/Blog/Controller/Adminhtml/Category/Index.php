<?php

namespace SM\Blog\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;

class Index extends \SM\Blog\Controller\Adminhtml\Category\Category
{

    const ADMIN_RESOURCE = 'SM_Blog::category_view';

    public function execute()
    {
        $resultPage = $this->context->getResultFactory()->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage);
        $this->_addContent($resultPage->getLayout()->createBlock('\SM\Blog\Block\Adminhtml\Category'));
        return $resultPage;
    }
}
