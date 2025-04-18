<?php declare(strict_types=1);

namespace ProxiBlue\HyvaSmileAutocompleteAddToCart\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\Data\Form\FormKey;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Psr\Log\LoggerInterface;

// phpcs:disable Generic.Files.LineLength.TooLong

class ItemFactory
{

    /**
     * Cart helper to get add to cart url
     *
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * Generate an up-to-date form key
     *
     * @var FormKey
     */
    protected $formKey;

    /**
     * Although depricated, magento core is still using this: Magento\Catalog\Block\Product\AbstractProduct
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Cached stock data
     *
     * @var null
     */
    private $stockItem = null;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param CartHelper $cartHelper
     * @param FormKey $formKey
     */
    public function __construct(
        CartHelper          $cartHelper,
        FormKey             $formKey,
        StockRegistryInterface $stockRegistry,
        LoggerInterface $logger
    )
    {
        $this->cartHelper = $cartHelper;
        $this->formKey = $formKey;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
    }

    /**
     * Inject the add to cart url here
     *
     * @param \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject Object
     * @param array $data Array of values
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, array $data)
    {
        try {
            if ($data['product']
                && $data['product']->isSaleable()
                && $data['product']->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ) {
                $this->stockItem = null;
                $data['qty_min'] = $this->getMinQty($data['product']);
                $data['qty_max'] = $this->getMaxQty($data['product']);
                $data['qty_increments_enabled'] = $this->areQtyIncrementsEnabled($data['product']);
                $data['qty_increments'] = $this->getQtyIncrements($data['product']);
                $data['qty_len'] = $this->getMaxQtyLength($data['product']);
                $data['qty_step'] = $this->getQtyStep($data['product']);
                $data['qty_format'] = $this->getQtyFormat($data['product']);
                $data['add_url'] = $this->cartHelper->getAddUrl($data['product'], ['useUencPlaceholder' => true]);
                $data['formkey'] = $this->formKey->getFormKey();
                $data['type'] = 'product_add_to_cart';
                $data['form_name'] = "ac_addtocart_" . $data['product']->getId();
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return [$data];
    }

    /**
     * @param ProductInterface $product
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockItem(ProductInterface $product)
    {
        if (empty($this->stockItem)) {
            $this->stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        }
        return $this->stockItem;
    }

    /**
     * @param ProductInterface $product
     * @return float|int|null
     */
    public function getMinQty(ProductInterface $product)
    {
        if (!($stockItem = $this->getStockItem($product))) {
            return 1;
        }
        $min = $stockItem->getMinSaleQty();
        return !empty($min)
            ? ($stockItem->getIsQtyDecimal() ? (float)$min : (int)$min)
            : 1;
    }

    /**
     * @param ProductInterface $product
     * @return float|int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMaxQty(ProductInterface $product)
    {
        if (!($stockItem = $this->getStockItem($product))) {
            return 999999;
        }
        $max = $stockItem->getMaxSaleQty();
        return !empty($max)
            ? ($stockItem->getIsQtyDecimal() ? (float)$max : (int)$max)
            : 999999;
    }

    /**
     * @param ProductInterface|Product $product
     * @return bool
     */
    public function areQtyIncrementsEnabled(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getEnableQtyIncrements();
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|float
     */
    public function getQtyIncrements(ProductInterface $product)
    {
        if (!($stockItem = $this->getStockItem($product))) {
            return 1;
        }
        return $stockItem->getQtyIncrements() ?: 1;
    }

    /**
     * @param ProductInterface $product
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMaxQtyLength(productInterface $product)
    {
        $maxQty = $this->getMaxQty($product);
        return ($maxQty ? strlen((string)$maxQty) : 4)
            + (/* add one if decimal for separator */
            (int)$this->isQtyDecimal($product));
    }

    /**
     * @param ProductInterface $product
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isQtyDecimal(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getIsQtyDecimal();
    }

    /**
     * @param ProductInterface $product
     * @return float|null
     */
    public function getQtyStep(productInterface $product)
    {
        $qtyIncrements = $this->getQtyIncrements($product);
        return $qtyIncrements ?: 1;
    }

    public function getQtyFormat(productInterface $product)
    {
        if ($this->isQtyDecimal($product)) {
            $result = ['type' => "text",
                'pattern' => "[0-9](\.[0-9])?{0," . $this->getMaxQty($product) . "}'",
                'inputmode' => "decimal"
            ];
        } else {
            $result = ['type' => "number",
                'pattern' => "[0-9]{0," . $this->getMaxQty($product) . "}",
                'inputmode' => "numeric"
            ];
        }
        return $result;
    }
}
