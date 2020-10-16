<?php

namespace SM\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use SM\Blog\Model\BlogFactory;

class Export extends \Magento\Backend\App\Action
{
    protected $uploaderFactory;

    protected $blogFactory;
    /**
     * @var FileFactory
     */
    protected $_fileFactory;
    /**
     * @var WriteInterface
     */
    protected $directory;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        BlogFactory $blogFactory // This is returns Collection of Data
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->blogFactory = $blogFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR); // VAR Directory Path
        parent::__construct($context);
    }
    public function execute()
    {
        $name = date('m-d-Y-H-i-s');
        $filepath = 'export/export-data-' . $name . '.csv'; // at Directory path Create a Folder Export and FIle
        try {
            $this->directory->create('export');
        } catch (FileSystemException $e) {
        }

        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        //column name display in your CSV

        $columns = ['BlogId','Name','Short Description','Thumbnail','Status','URL Key','Publish From','Publish To'];

        foreach ($columns as $column) {
            $header[] = $column; //storeColumn in Header array
        }

        try {
            $stream->writeCsv($header);
        } catch (FileSystemException $e) {
        }

        $location = $this->blogFactory->create();
        $location_collection = $location->getCollection(); // get Collection of Table data

        foreach ($location_collection as $item) {
            $itemData = [];

            // column name must same as in your Database Table

            $itemData[] = $item->getData('id');
            $itemData[] = $item->getData('name');
            $itemData[] = $item->getData('short_description');
            $itemData[] = $item->getData('thumbnail');
            $itemData[] = $item->getData('status');
            $itemData[] = $item->getData('url_key');
            $itemData[] = $item->getData('publish_date_from');
            $itemData[] = $item->getData('publish_date_to');

            try {
                $stream->writeCsv($itemData);
            } catch (FileSystemException $e) {
            }
        }

        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder

        $csvFilename = 'locator-import-' . $name . '.csv';
        return $this->_fileFactory->create($csvFilename, $content, DirectoryList::VAR_DIR);
    }
}
