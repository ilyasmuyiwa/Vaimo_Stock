<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 8:30 AM
 */
namespace Vaimo\StockStatus\Plugin;
use Magento\Store\Model\StoreManagerInterface;
use Vaimo\StockStatus\Api\ResponseInterface;
class StockStatusValidator
{

    protected $storeManagerInterface;
    protected $responseInterface;

    public function __construct(
        StoreManagerInterface $storeManager,
        ResponseInterface $responseInterface)
    {
        $this->responseInterface = $responseInterface;
        $this->storeManagerInterface = $storeManager;
    }

    public function aroundValidate(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();
        $sku = $quoteItem->getSku();
        $url = $this->storeManagerInterface->getStore()->getBaseUrl().'vaimo/api/index?sku='.$sku;
        $response = $this->responseInterface->getResponse($url);
        $inStock = $response[0]["instock"];

        if ($inStock == ResponseInterface::MINIMUM_STOCK) {
            $quoteItem->addErrorInfo(
                'cataloginventory',
                \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                __('This product is not in vaimo stock')
            );
            $quoteItem->getQuote()->addErrorInfo(
                'stock',
                'cataloginventory',
                \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                __('Some of the products are not in vaimo stock.')
            );
            return; //
        }

        return $proceed($observer);
    }
}