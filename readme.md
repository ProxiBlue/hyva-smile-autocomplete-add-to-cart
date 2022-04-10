# Autocomplete Add To Cart Button

## Introduction

Place an add to cart button on Autocomplete for saleable simple products

![image](https://user-images.githubusercontent.com/4994260/162602809-daa6c629-84bc-415a-b05f-c5f5d88aa5c9.png)


## Requirements

* Hyva Theme for Magento
* Smile ElasticSuite Search with Autocomplete enabled
* Hyva Smile Compatibility module

## Install

* composer config repositories.github.repo.repman.io composer https://github.repo.repman.io
* composer require proxi-blue/hyva-smile-autocomplete-add-to-cart
* ./bin/magento setup:upgrade
* ./bin/magento setup:di:compile

## Configuration

* None, once installed, add to cart mini form will be added to all saleable simple products
* Quantity will adhere to product configured min/max and qty step configuration.
* Ads in a new render template for the add to cart rows, so existing product row renders are not affected. (ProxiBlue_HyvaSmileAutocompleteAddToCart::catalog/autocomplete/product_add_to_cart.phtml)
* Will return to current page after add to cart form submit was effected. ( Coudl have ajax'd it, but this works just as well )

## Donations

If you use this in a commercial site, I'd appreciate a donation to my Cat Sugar Cube's toy fund ;)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://paypal.me/proxiblue?locale.x=en_AU)

![image](https://user-images.githubusercontent.com/4994260/119922080-abece100-bfa1-11eb-968e-79af6e94789a.png)
