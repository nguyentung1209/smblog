<?php

namespace SM\Blog\Plugin;

use Magento\Customer\Model\Session;

class ChangePriceProduct
{

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $result * 2;
    }
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $result . "_Smart_OSC";
    }

}
