<?php

namespace SM\Blog\Block\Sidebar;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\Blog\Model\Url;
use SM\Blog\Model\ResourceModel\BlogCategory\CollectionFactory;

class CategoryTree extends Template
{

    protected $data;

    protected $categoryCollection;
    /**
     * @var string
     */
    protected $htmlSlelect = '';

    public function __construct(
        Context $context,
        CollectionFactory $categoryCollection,
        $data
    ) {
        $this->data = $data;
        $this->categoryCollection = $categoryCollection;
        parent::__construct($context);
    }
    public function categoryRecusive($parentId, $id = 2, $text = "")
    {
        $this->data = $this->categoryCollection->create()
            ->addFieldToFilter('blog_category_id', ['neq' => 2])
            ->addFieldToFilter('blog_category_id', ['neq' => 1])
            ->getData();
        foreach ($this->data as $value) {
            if ($value['parent_id'] == $id) {
                if (!empty($parentId) && $parentId == $value['blog_category_id']) {
                    $this->htmlSlelect .= "<li><a href='/blog/category/" . $value['url_key'] . ".html" . "'>" . $text . $value['name'] . "</a></li>";
                } else {
                    $this->htmlSlelect .= "<li><a href='/blog/category/" . $value['url_key'] . ".html" . "'>" . $text . $value['name'] . "</a></li>";
                }

                $this->categoryRecusive($parentId, $value['blog_category_id'], $text . "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
            }
        }
        return $this->htmlSlelect;
    }
}
