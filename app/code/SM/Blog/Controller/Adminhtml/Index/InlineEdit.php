<?php


namespace SM\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use SM\Blog\Model\BlogFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM::blog';

    /**
     * @var \SM\Blog\Model\BlogFactory
     */
    protected $blogFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param BlogFactory $blogFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        BlogFactory $blogFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->blogFactory = $blogFactory;
        $this->jsonFactory = $jsonFactory;
    }
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $blogId) {
                    /** @var \SM\Blog\Model\Blog $blog */
                    $blog = $this->blogFactory->create()->load($blogId);
                    try {
                        $blog->setData(array_merge($blog->getData(), $postItems[$blogId]));
                        $blog->save();
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithBlogId(
                            $blog,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
    protected function getErrorWithBlogId(\SM\Blog\Model\Blog $blog, $errorText)
    {
        return '[Blog ID: ' . $blog->getId() . '] ' . $errorText;
    }
}
