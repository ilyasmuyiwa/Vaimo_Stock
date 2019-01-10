<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 2:07 PM
 */

namespace Vaimo\StockStatus\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;


class InstallData implements InstallDataInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;

    protected $state;

    public function __construct(

        ProductFactory $productFactory,
        StoreManagerInterface $storeManagerInterface,
        LoggerInterface $logger,
        State $state
    )
    {
        $this->productFactory = $productFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->logger = $logger;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $products = [
            'First Product' => 'A00001',
            'Third Product' => 'A00003',
            'Fifth Product' => 'A00005'

        ];
        $categories = array(2);
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        foreach ($products as $name => $sku) {


            $data = [
                'name' => $name,
                'sku' => $sku,
                'price' => 1000,
                'weight' => 20
            ];
            $attributeSetId = 4; //Attribute set default
            $product = $this->productFactory->create();
            $product->setData($data);
            $product->setCategoryIds($categories);
            $product
                ->setTypeId($this->productType)
                ->setAttributeSetId($attributeSetId)
                ->setWebsiteIds([$this->storeManagerInterface->getDefaultStoreView()->getWebsiteId()])
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setStockData(['is_in_stock' => 1, 'manage_stock' => 0, 'qty' => 50])
                ->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

            if (empty($data['visibility'])) {
                $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            }
            try{
                $product->save();

            }catch (\Exception $e){
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product_install.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
    }
}