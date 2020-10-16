<?php

namespace SM\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use SM\Blog\Helper\Helper;
use SM\Blog\Model\BlogCategoryFactory;
use SM\Blog\Model\ResourceModel\UrlRewrite\CollectionFactory as UrlRewriteCollection;
use SM\Blog\Model\UrlRewriteFactory;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var BlogCategoryFactory
     */
    protected $blogCategoryFactory;

    protected $urlRewriteFactory;

    protected $urlRewriteCollection;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        BlogCategoryFactory $blogCategoryFactory = null,
        UrlRewriteFactory $urlRewriteFactory,
        UrlRewriteCollection $urlRewriteCollection
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->blogCategoryFactory = $blogCategoryFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(BlogCategoryFactory::class);
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlRewriteCollection = $urlRewriteCollection;
        parent::__construct($context);
    }
    public function execute()
    {
        $lastId = null;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
//        var_dump($data);
//        die();
        if ($data) {
            if (empty($data['blog_category_id'])) {
                $data['blog_category_id'] = null;
            }
            if ($data['url_key'] == null) {
                $data['url_key'] = Helper::convertToURL($data['name']);
            }
            $parent_id = $data['data']['parent'][0];
            /** @var \SM\Blog\Model\BlogCategory $model */
            $model = $this->blogCategoryFactory->create();
            $parent_path = $model->load($parent_id)->getPath();
            $parent_level = $model->load($parent_id)->getLevel();
            $data['parent_id'] = $parent_id;
            $data['level'] = $parent_level + 1;
            $id = $data['blog_category_id'];
            if ($id) {
                try {
                    $data['path'] = $parent_path . "/" . $id;
                    $model = $model->load($id)->setData($data);
                    try {
                        $model->save();
                        $this->saveUrlRewrite($id, $data);
                        $this->messageManager->addSuccessMessage(__('You saved the blog category.'));
                        $this->dataPersistor->clear('blog_category');
                        return $this->processBlockReturn($model, $data, $resultRedirect);
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the blog category.'));
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This block no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $model->addData($data);
                try {
                    $model->save();
                    $lastId = $model->getId();
                    $lastPath = $model->load($lastId)->getPath() . "/" . $lastId;
                    $model->load($lastId)->setPath($lastPath);
                    $model->save();
                    $this->saveUrlRewrite($lastId, $data);
                    $this->messageManager->addSuccessMessage(__('You saved the blog category.'));
                    $this->dataPersistor->clear('blog_category');
                    return $this->processBlockReturn($model, $data, $resultRedirect);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the blog category.'));
                }
            }
            $this->dataPersistor->set('blog_category', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    private function processBlockReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
//        else if ($redirect === 'duplicate') {
//            $duplicateModel = $this->blogCategoryFactory->create(['data' => $data]);
//            $duplicateModel->setId(null);
//            $duplicateModel->setShortDescription($data['short_description'] . '-' . uniqid());
//            $duplicateModel->setStatus(Blog::STATUS_DISABLED);
//            $this->blogRepository->save($duplicateModel);
//            $id = $duplicateModel->getId();
//            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
//            $this->dataPersistor->set('blog', $data);
//            $resultRedirect->setPath('*/*/edit', ['id' => $id]);
//        }
        return $resultRedirect;
    }
    protected function checkExistUrlRewrite($requestPath)
    {
        $url = $this->urlRewriteCollection->create()
            ->addFieldToFilter('request_path', $requestPath)
            ->getData();
        if (empty($url)) {
            return false;
        }
        return $url[0]['url_rewrite_id'];
    }
    protected function saveUrlRewrite($categoryLastId, $data)
    {
        $modelUrlRewrite = $this->urlRewriteFactory->create();
        if (isset($data['url_key'])) {
            if ($this->checkExistUrlRewrite("blog/category/" . $data['url_key'] . ".html") != false) {
                $url_rewrite_id = $this->checkExistUrlRewrite($categoryLastId);
                $modelUrlRewrite->load($url_rewrite_id);
                $modelUrlRewrite->delete();
                $modelUrlRewrite
                    ->setData('request_path', "blog/category/" . $data['url_key'] . ".html")
                    ->setData('entity_type', 'custom')
                    ->setData('target_path', "blog/category/index/id/" . $categoryLastId)
                    ->setData('store_id', 1)
                    ->setData('entity_id', $categoryLastId);
                $modelUrlRewrite->save();
            } else {
                $dataUrlRewrite = [
                    'entity_type' => 'custom',
                    'entity_id' => $categoryLastId,
                    'request_path' => "blog/category/" . $data['url_key'] . ".html",
                    'target_path' => "blog/category/index/id/" . $categoryLastId,
                    'store_id' => 1
                ];
                $modelUrlRewrite->addData($dataUrlRewrite);
                $modelUrlRewrite->save();
            }
        }
    }
}
