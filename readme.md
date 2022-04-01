# Autocomplete Add To Cart Button

## Introduction

Place an add to cart button on Autocomplete for saleable simple products

![image](https://user-images.githubusercontent.com/4994260/161305333-7c9cf03e-c384-4d02-a94e-ab18327202fb.png)


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

None, once installed, button will be added to all simple products
