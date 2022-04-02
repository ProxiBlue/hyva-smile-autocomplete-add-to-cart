<?php declare(strict_types=1);

namespace ProxiBlue\HyvaSmileAutocompleteAddToCart\Plugin;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\Data\Form\FormKey;

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
     * Constructor
     *
     * @param CartHelper $cartHelper
     * @param FormKey $formKey
     */
    public function __construct(
        CartHelper $cartHelper,
        FormKey    $formKey
    ) {
        $this->cartHelper = $cartHelper;
        $this->formKey = $formKey;
    }

    /**
     * Inject the add to cart url here
     *
     * @param \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject Object
     * @param array                                                             $data    Array of values
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, array $data)
    {
        if ($data['product']
            && $data['product']->isSaleable()
            && $data['product']->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
        ) {
            $data['add_url'] = $this->cartHelper->getAddUrl($data['product'], ['useUencPlaceholder' => true]);
            $data['formkey'] = $this->formKey->getFormKey();
            $data['type'] = 'product_add_to_cart';
        }

        return [$data];
    }
}
