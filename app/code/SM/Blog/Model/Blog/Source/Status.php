<?php


namespace SM\Blog\Model\Blog\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    protected $blog;

    public function __construct(\SM\Blog\Model\Blog $blog)
    {
        $this->blog = $blog;
    }

    public function toOptionArray()
    {
        $availableOptions = $this->blog->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
