<?php

namespace SM\Blog\Controller\Adminhtml\Gallery;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends Action implements HttpPostActionInterface
{
    protected $resultRawFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);

            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

            $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');

            $uploader->setAllowRenameFiles(true);

            $uploader->setFilesDispersion(true);

            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);

            $config = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');

            $pth = $mediaDirectory->getAbsolutePath('tmp/comingsoon/maintenance/images');

            $result = $uploader->save($mediaDirectory->getAbsolutePath('tmp/comingsoon/maintenance/images'));

            $imgpath = $this->_prepareFile($result['file']);

            $imgpathArray = explode("/", $imgpath);

            unset($imgpathArray[count($imgpathArray)-1]);

            $imgpath = implode("/", $imgpathArray);

            $this->chmod_r($result['path'] . '/' . $imgpath);

            chmod($result['path'] . '/' . $this->_prepareFile($result['file']), 0777);

            unset($result['tmp_name']);

            unset($result['path']);

            $url = $this->_objectManager->get('Magento\Framework\UrlInterface')->getBaseUrl() . $this->getBaseMediaUrlAddition();

            $result['url'] =   $url . '/' . $this->_prepareFile($result['file']);

            $result['file'] = $result['file'];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        $response = $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory')->create();

        $response->setHeader('Content-type', 'text/plain');

        $response->setContents(json_encode($result));

        return $response;
    }

    private function chmod_r($path)
    {
        $dir = new \DirectoryIterator($path);

        foreach ($dir as $item) {
            chmod($item->getPathname(), 0777);

            if ($item->isDir() && !$item->isDot()) {
                chmod_r($item->getPathname());
            }
        }
    }

    private function getBaseMediaUrlAddition()
    {
        return 'pub/media/blog';
    }

    private function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
