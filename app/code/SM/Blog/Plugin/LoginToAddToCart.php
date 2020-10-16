<?php

namespace SM\Blog\Plugin;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;

class LoginToAddToCart
{
    protected $_session;

    public function __construct(Session $session)
    {
        $this->_session = $session;
    }

    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null)
    {
//        $requestInfo['qty'] = $requestInfo['qty'] * 2;
        return [$productInfo, $requestInfo];
    }
//    public function afterAddProduct(Cart $subject, $productInfo, $requestInfo = null)
//    {
//        return [$productInfo, $requestInfo * 2];
//    }
//    public function beforeGetQtyRequest(Cart $subject, $product, $request = 0){
//        return [$product, $request * 2];
//    }
}
