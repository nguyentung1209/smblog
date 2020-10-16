<?php


namespace SM\Blog\Model\Tags\Source;

use Magento\Framework\Option\ArrayInterface;
use SM\Blog\Model\ResourceModel\BlogTags\CollectionFactory;

class Tags implements ArrayInterface
{
    private $tagFactory;

    public function __construct(CollectionFactory $tagFactory)
    {
        $this->tagFactory = $tagFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->tagFactory->create()->addFieldToSelect('*')->getData() as $tag) {
            $result[] = [
                'label' => $tag['tags'],
                'value' => $tag['blog_tags_id'],
            ];
        }

        return $result;
    }
}
