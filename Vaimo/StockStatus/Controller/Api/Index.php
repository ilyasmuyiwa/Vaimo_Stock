<?php

namespace Vaimo\StockStatus\Controller\Api;

use Magento\Framework\Controller\Result\JsonFactory;
use Vaimo\StockStatus\Api\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

class Index extends Action
{

    protected $jsonFactory;

    protected $responseInterface;

    /**
     * Index constructor.
     * @param JsonFactory $jsonFactory
     * @param Context $context
     * @param ResponseInterface $responseInterface
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Context $context,
        ResponseInterface $responseInterface)
    {
        $this->jsonFactory = $jsonFactory;
        $this->responseInterface = $responseInterface;
        parent::__construct($context);
    }

    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');
        $response = $this->responseInterface->getResponse(ResponseInterface::VAIMO_API.'?sku='.$sku);
        return $this->jsonFactory->create()->setData(($response));

    }

}