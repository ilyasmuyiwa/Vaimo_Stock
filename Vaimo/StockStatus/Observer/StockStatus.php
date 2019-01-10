<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/8/2018
 * Time: 10:05 PM
 */

namespace Vaimo\StockStatus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Vaimo\StockStatus\Api\ResponseInterface;

class StockStatus implements ObserverInterface{

    protected $storeManagerInterface;
    protected $responseInterface;

    public function __construct(
        StoreManagerInterface $storeManager,
        ResponseInterface $responseInterface )
    {
        $this->responseInterface = $responseInterface;
        $this->storeManagerInterface = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item){
            $sku = $item->getSku();
            $url = $this->storeManagerInterface->getStore()->getBaseUrl()
                .'vaimo/api/index?sku='.$sku;
            $response = $this->responseInterface->getResponse($url);
            if ($response[0]["instock"] == \Vaimo\StockStatus\Api\ResponseInterface::MINIMUM_STOCK)
                exit; //Redirect back to cart page.
        }

    }
}