<?php declare(strict_types=1);

namespace ProxiBlue\HyvaSmileAutocompleteAddToCart\Plugin;

use Magento\Checkout\Helper\Cart as CartHelper;

class ItemFactory
{

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @param CartHelper $cartHelper
     */
    public function __construct(
        CartHelper $cartHelper
    ) {
        $this->cartHelper             = $cartHelper;
    }

    /**
     * Inject thw add to cart url here
     *
     * @param \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject
     * @param callable $proceed
     * @param $data
     * @return mixed
     */
    public function aroundCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, callable $proceed, $data)
    {
        if ($data['product']
            && $data['product']->isSaleable()
            && $data['product']->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
            $data['add_url'] = $this->cartHelper->getAddUrl($data['product'], ['useUencPlaceholder'=>true]);
            $data['type'] = 'product_add_to_cart';
        }
        return $proceed($data);
    }
}
