<?php

namespace SM\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use SM\Blog\Helper\Helper;
use SM\Blog\Model\Blog;
use SM\Blog\Model\BlogFactory;
use SM\Blog\Model\BlogGalleryFactory;
use SM\Blog\Model\BlogRelatedCategoryBlogFactory;
use SM\Blog\Model\BlogRelatedGalleryBlogFactory;
use SM\Blog\Model\BlogRelatedProductBlogFactory;
use SM\Blog\Model\BlogRelatedTagsBlogFactory;
use SM\Blog\Model\BlogTagsFactory;
use SM\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollection;
use SM\Blog\Model\ResourceModel\BlogGallery\CollectionFactory as GalleryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedCategoryBlog\CollectionFactory as BlogCategoryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedGalleryBlog\CollectionFactory as BlogGalleryCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedProductBlog\CollectionFactory as BlogProductCollection;
use SM\Blog\Model\ResourceModel\BlogRelatedTagsBlog\CollectionFactory as BlogTagsCollection;
use SM\Blog\Model\ResourceModel\BlogTags\CollectionFactory;
use SM\Blog\Model\ResourceModel\UrlRewrite\CollectionFactory as UrlRewriteCollection;
use SM\Blog\Model\UrlRewriteFactory;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'SM_Blog::save';
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    protected $blogFactory;

    protected $tagsFactory;

    protected $tagsCollectionFactory;

    protected $blogCategoryFactory;

    protected $blogTagsFactory;

    protected $blogProductFactory;

    protected $blogCategoryCollection;

    protected $blogTagsCollection;

    protected $blogProductCollection;

    protected $blogCollection;

    protected $galleryCollection;

    protected $urlRewriteFactory;

    protected $urlRewriteCollection;

    protected $blogGalleryCollection;

    protected $galleryFactory;

    protected $blogGalleryFactory;
    /**
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        BlogFactory $blogFactory,
        BlogRelatedCategoryBlogFactory $blogCategoryFactory,
        BlogRelatedTagsBlogFactory $blogTagsFactory,
        BlogRelatedProductBlogFactory $blogProductFactory,
        CollectionFactory $tagsCollectionFactory,
        BlogTagsFactory $tagsFactory,
        BlogCategoryCollection $blogCategoryCollection,
        BlogTagsCollection $blogTagsCollection,
        BlogProductCollection $blogProductCollection,
        BlogCollection $blogCollection,
        UrlRewriteFactory $urlRewriteFactory,
        UrlRewriteCollection $urlRewriteCollection,
        GalleryCollection $galleryCollection,
        BlogGalleryCollection $blogGalleryCollection,
        BlogGalleryFactory $galleryFactory,
        BlogRelatedGalleryBlogFactory $blogGalleryFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->blogFactory = $blogFactory;
        $this->blogCategoryFactory = $blogCategoryFactory;
        $this->blogTagsFactory = $blogTagsFactory;
        $this->blogProductFactory = $blogProductFactory;
        $this->tagsCollectionFactory = $tagsCollectionFactory;
        $this->tagsFactory = $tagsFactory;
        $this->blogCategoryCollection = $blogCategoryCollection;
        $this->blogTagsCollection = $blogTagsCollection;
        $this->blogProductCollection = $blogProductCollection;
        $this->blogCollection = $blogCollection;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlRewriteCollection = $urlRewriteCollection;
        $this->galleryCollection = $galleryCollection;
        $this->blogGalleryCollection = $blogGalleryCollection;
        $this->galleryFactory = $galleryFactory;
        $this->blogGalleryFactory = $blogGalleryFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
//        var_dump($data['thumbnail']);
//
//        var_dump($data['description']);
//        die();
        if ($data) {
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Blog::STATUS_ENABLED;
            }
            if (empty($data['id'])) {
                $data['id'] = null;
            }
            if ($data['url_key'] == null) {
                $data['url_key'] = Helper::convertToURL($data['name']);
            }
            if (isset($data['thumbnail'])) {
                $image = $data['thumbnail'];
                $data['thumbnail'] = $image[0]['name'];
                $checkImage = true;
            } else {
                // default thumbnail
                $data['thumbnail'] = 'default.jpg';
                $image[] = [
                    'name' => 'default.jpg',
                    'url' => 'http://localhost:8888/media/blog/default.jpg',
                    'size' => '49409',
                    'type' => 'image',
                    'previewType' => 'image' ,
                    'id' => 'ZGVmYXVsdC5qcGc,'
                ];
                $checkImage = false;
            }
            $modelBlog = $this->blogFactory->create();
            $modelTagsRelated = $this->blogTagsFactory->create();
            $modelCategory = $this->blogCategoryFactory->create();
            $modelProduct = $this->blogProductFactory->create();
            $modelGallery = $this->blogGalleryFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $modelBlog = $modelBlog->load($id)->setData($data);
                    try {
                        if ($this->checkExistNameBlog($data['name']) && $data['name'] !== $this->getBlogNameById($id)) {
                            $this->messageManager->addErrorMessage(__('Blog name already exist!'));
                        } else {
                            if ($checkImage) {
                                if (Helper::uploadImage($image[0]) !== true) {
                                    $this->messageManager->addErrorMessage(__(implode("</br>", Helper::uploadImage($image))));
                                } else {
                                    $modelBlog->save();
                                    if (isset($image[0]['tmp_name'])) {
                                        move_uploaded_file($image[0]['tmp_name'], "/pub/media/blog/" . $image[0]['name']);
                                    }
                                    $collectionCategory = $this->blogCategoryCollection->create();
                                    $relatedCategory = $collectionCategory->addFieldToFilter('id', $id)->getData();
                                    $collectionTags = $this->blogTagsCollection->create();
                                    $relatedTags = $collectionTags->addFieldToFilter('id', $id)->getData();
                                    $collectionProduct = $this->blogProductCollection->create();
                                    $relatedProduct = $collectionProduct->addFieldToFilter('id', $id)->getData();
                                    $collectionGallery = $this->blogGalleryCollection->create();
                                    $blogGallery = $collectionGallery->addFieldToFilter('id', $id)->getData();
                                    foreach ($relatedCategory as $item) {
                                        $modelCategory->load($item['related_id']);
                                        $modelCategory->delete();
                                    }
                                    foreach ($relatedTags as $item) {
                                        $modelTagsRelated->load($item['related_id']);
                                        $modelTagsRelated->delete();
                                    }
                                    foreach ($relatedProduct as $item) {
                                        $modelProduct->load($item['related_id']);
                                        $modelProduct->delete();
                                    }
                                    foreach ($blogGallery as $item) {
                                        $modelGallery->load($item['related_id']);
                                        $modelGallery->delete();
                                    }
                                    $this->saveDataByBlogId($id, $data);
                                    $this->messageManager->addSuccessMessage(__('You saved the blog.'));
                                    $this->dataPersistor->clear('blog');
                                    return $this->processBlockReturn($modelBlog, $data, $resultRedirect);
                                }
                            } else {
                                $modelBlog->save();
                                $collectionCategory = $this->blogCategoryCollection->create();
                                $relatedCategory = $collectionCategory->addFieldToFilter('id', $id)->getData();
                                $collectionTags = $this->blogTagsCollection->create();
                                $relatedTags = $collectionTags->addFieldToFilter('id', $id)->getData();
                                $collectionProduct = $this->blogProductCollection->create();
                                $relatedProduct = $collectionProduct->addFieldToFilter('id', $id)->getData();
                                foreach ($relatedCategory as $item) {
                                    $modelCategory->load($item['related_id']);
                                    $modelCategory->delete();
                                }
                                foreach ($relatedTags as $item) {
                                    $modelTagsRelated->load($item['related_id']);
                                    $modelTagsRelated->delete();
                                }
                                foreach ($relatedProduct as $item) {
                                    $modelProduct->load($item['related_id']);
                                    $modelProduct->delete();
                                }
                                $this->saveDataByBlogId($id, $data);
                                $this->messageManager->addSuccessMessage(__('You saved the blog.'));
                                $this->dataPersistor->clear('blog');
                                return $this->processBlockReturn($modelBlog, $data, $resultRedirect);
                            }
                        }
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the blog.'));
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This blog no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $modelBlog->addData($data);
                try {
                    if ($this->checkExistNameBlog($data['name'])) {
                        $this->messageManager->addErrorMessage('Blog name already exist!');
                    } else {
                        if ($checkImage) {
                            if (Helper::uploadImage($image[0]) !== true) {
                                $this->messageManager->addErrorMessage(implode("</br>", Helper::uploadImage($image[0])));
                            } else {
                                $modelBlog->save();
                                if (isset($image[0]['tmp_name'])) {
                                    move_uploaded_file($image[0]['tmp_name'], "/pub/media/blog/" . $image[0]['name']);
                                }
                                $blogLastId = $modelBlog->getId();
                                $this->saveDataByBlogId($blogLastId, $data);
                                $this->messageManager->addSuccessMessage(__('You saved the blog.'));
                                $this->dataPersistor->clear('blog');
                                return $this->processBlockReturn($modelBlog, $data, $resultRedirect);
                            }
                        } else {
                            $modelBlog->save();
                            $blogLastId = $modelBlog->getId();
                            $this->saveDataByBlogId($blogLastId, $data);
                            $this->messageManager->addSuccessMessage(__('You saved the blog.'));
                            $this->dataPersistor->clear('blog');
                            return $this->processBlockReturn($modelBlog, $data, $resultRedirect);
                        }
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the blog.'));
                }
            }
            $data['thumbnail'] = $image;
            $this->dataPersistor->set('blog', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    protected function processBlockReturn($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
//        else if ($redirect === 'duplicate') {
//            $duplicateModel = $this->blogFactory->create(['data' => $data]);
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
    protected function checkTags($tagId)
    {
        $tags = $this->tagsCollectionFactory->create()->addFieldToFilter('blog_tags_id', $tagId)->getData();
        if (empty($tags)) {
            return false;
        }
        return true;
    }
    protected function saveDataByBlogId($blogLastId, $data)
    {
        $modelTags = $this->tagsFactory->create();
        $modelTagsRelated = $this->blogTagsFactory->create();
        $modelCategory = $this->blogCategoryFactory->create();
        $modelProduct = $this->blogProductFactory->create();
        $modelUrlRewrite = $this->urlRewriteFactory->create();
        $modelGallery = $this->galleryFactory->create();
        $modelGalleryBlog = $this->blogGalleryFactory->create();
        // save url rewrite
        if (isset($data['url_key'])) {
            if ($this->checkExistUrlRewrite("blog/" . $data['url_key'] . ".html") != false) {
                $url_rewrite_id = $this->checkExistUrlRewrite($blogLastId);
                $modelUrlRewrite->load($url_rewrite_id);
                $modelUrlRewrite->delete();
                $modelUrlRewrite
                    ->setData('request_path', "blog/" . $data['url_key'] . ".html")
                    ->setData('entity_type', 'custom')
                    ->setData('target_path', "blog/index/detail/id/" . $blogLastId)
                    ->setData('store_id', 1)
                    ->setData('entity_id', $blogLastId);
                $modelUrlRewrite->save();
            } else {
                $dataUrlRewrite = [
                'entity_type' => 'custom',
                'entity_id' => $blogLastId,
                'request_path' => "blog/" . $data['url_key'] . ".html",
                'target_path' => "blog/index/detail/id/" . $blogLastId,
                'store_id' => 1
            ];
                $modelUrlRewrite->addData($dataUrlRewrite);
                $modelUrlRewrite->save();
            }
        }
        // save category blog
        if (isset($data['data']['parent'])) {
            $data['categories'] = $data['data']['parent'];
            foreach ($data['categories'] as $category) {
                $blogCategory = [
                    'id' => $blogLastId,
                    'blog_category_id' => $category
                ];
                $modelCategory->setData($blogCategory);
                $modelCategory->save();
            }
        }
        // save tags blog
        if (!empty($data['tag_ids'])) {
            for ($i = 0; $i < count($data['tag_ids']); $i++) {
                if (!$this->checkTags($data['tag_ids'][$i])) {
                    $newTags = ['tags' => $data['tag_ids'][$i]];
                    $modelTags->setData($newTags);
                    $modelTags->save();
                    $data['tag_ids'][$i] = $modelTags->getId();
                }
            }
            foreach ($data['tag_ids'] as $item) {
                $blogTags = [
                    'id' => $blogLastId,
                    'blog_tags_id' => $item
                ];
                $modelTagsRelated->setData($blogTags);
                $modelTagsRelated->save();
            }
        }
        // save related product
        if (isset($data['blog_related_product_listing'])) {
            $data['products'] = [];
            foreach ($data['blog_related_product_listing'] as $item) {
                array_push($data['products'], $item['entity_id']);
            }
            foreach ($data['products'] as $item) {
                $blogProduct = [
                    'id' => $blogLastId,
                    'product_id' => $item
                ];
                $modelProduct->setData($blogProduct);
                $modelProduct->save();
            }
        }
        // save gallery
        if (isset($data['gallery'])) {
            $galleryIds = [];
            for ($i = 0; $i < count($data['gallery']); $i++) {
                if (Helper::uploadImage($data['gallery'][$i]) !== true) {
                    $this->messageManager->addErrorMessage(implode("</br>", Helper::uploadImage($data['gallery'][$i])));
                } else {
                    if (isset($data['gallery'][$i]['tmp_name'])) {
                        move_uploaded_file($data['gallery'][$i]['tmp_name'], "/pub/media/blog/" . $data['gallery'][$i]['name']);
                    }
                    $newGallery = ['path' => $data['gallery'][$i]['name']];
                    $modelGallery->setData($newGallery);
                    $modelGallery->save();
                    array_push($galleryIds, $modelGallery->getId());
                }
            }
            foreach ($galleryIds as $item) {
                $blogGallery = [
                    'id' => $blogLastId,
                    'blog_gallery_id' => $item
                ];
                $modelGalleryBlog->setData($blogGallery);
                $modelGalleryBlog->save();
            }
        }
    }
    protected function checkExistNameBlog($blogName)
    {
        $blogName = $this->blogCollection->create()->addFieldToFilter('name', $blogName)->getData();
        if (empty($blogName)) {
            return false;
        }
        return true;
    }
    protected function getBlogNameById($blogId)
    {
        return $this->blogCollection->create()->addFieldToFilter('id', $blogId)->getData()[0]['name'];
    }
    protected function checkExistUrlRewrite($requestPath)
    {
        $url = $this->urlRewriteCollection->create()
            ->addFieldToFilter('request_path', $requestPath)
            ->addFieldToFilter('entity_type', 'custom')
            ->getData();
        if (empty($url)) {
            return false;
        }
        return $url[0]['url_rewrite_id'];
    }
}
