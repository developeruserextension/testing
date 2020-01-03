#!/bin/bash
php -f bin/magento maintenance:enable
sleep 10
rm -rf ./pub/static/*
rm -rf ./var/cache/*
rm -rf ./var/generation/*
rm -rf ./generated/*
rm -rf ./var/page_cache/*
rm -rf ./var/view_preprocessed/*
php -f bin/magento deploy:mode:set production
php -f bin/magento setup:upgrade
php -f bin/magento setup:di:compile
php -f bin/magento setup:static-content:deploy -f
php -f bin/magento indexer:reindex
php -f bin/magento cache:clean
php -f bin/magento cache:flush
php -f bin/magento maintenance:disable
