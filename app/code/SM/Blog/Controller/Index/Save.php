<?php

namespace SM\Blog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use SM\Blog\Model\UrlRewriteFactory;

class Save extends Action
{
    protected $resultPageFactory;
    protected $urlRewrite;

    public function __construct(Context $context, PageFactory $resultPageFactory, UrlRewriteFactory $urlRewrite)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->urlRewrite = $urlRewrite;
        return parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $newData = [
            'entity_type' => 'custom',
            'entity_id' => 1,
            'request_path' => 'test.html',
            'target_path' => 'blog/index/detail/id/1',
            'store_id' => 1
        ];

        $urlRewrite = $this->urlRewrite->create();

        try {
            $urlRewrite->addData($newData);
            $urlRewrite->save();
            return $this->_redirect('blog/index/index');
        } catch (\Exception $e) {
            $this->addErrorMessage(__('Save Failed'));
        }
    }
}
