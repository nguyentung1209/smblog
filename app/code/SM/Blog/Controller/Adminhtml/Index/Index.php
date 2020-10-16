<?php
namespace SM\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use SM\Blog\Model\ResourceModel\BlogRelatedProductBlog\CollectionFactory;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Index extends Action
{

    const ADMIN_RESOURCE = 'SM_Blog::view';
    protected $tagsFactory;
    protected $resultPageFactory;
    protected $categoryCollectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CategoryCollection $categoryCollectionFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function execute()
    {
        // Load layout and set active menu
        $resultPage = $this->resultPageFactory->create();
//        $resultPage->setActiveMenu('SM::blog_manager');
        $resultPage->getConfig()->getTitle()->prepend(__('Blog Manager'));
        return $resultPage;
//        $categories = $this->categoryCollectionFactory->create()
//            ->addFieldToSelect('blog_category_id','name')
//            ->toOptionArray();
//        var_dump($categories);
    }

}
