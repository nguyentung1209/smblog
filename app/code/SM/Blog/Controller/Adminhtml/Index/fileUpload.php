<?php


namespace SM\Blog\Controller\Adminhtml\Index;


use \Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use SM\Blog\Model\Config\FileProcessor;

class fileUpload extends Action
{
    private $fileProcessor;

    public function __construct(
        FileProcessor $fileProcessor,
        Context $context
    ) {
        $this->fileProcessor = $fileProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->fileProcessor->save(key($_FILES));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
