<?php

namespace SM\Blog\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;

class loginToAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    protected $_session;

    public function __construct(Session $session)
    {
        $this->_session = $session;
    }
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getData('product');
        if (strpos($product->getSku(), ' 24-WB04') === false) {
            return;
        }
        $quote_item = $observer->getEvent()->getData('quote_item');
        $price = $quote_item->getPrice();
        $quote_item->setBasePrice($price * 2);
        $quote_item->setCustomPrice($price * 2);
        $quote_item->setOriginalPrice($price * 2);
    }
}
