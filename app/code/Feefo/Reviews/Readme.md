### How to install?

1. Open up your terminal window and change current directory to root one of the Magento 2 instance.
2. Check the current mode of your Magento 2 instance:
`./bin/magento deploy:mode:show`
3. Change your current mode to developer, if it is different:
`./bin/magento deploy:mode:set developer`
4. Create new directories under ./app/code :
`mkdir ./app/code/Feefo/Reviews`
5. Copy files of the module to the Feefo directory.
6. Enable the module
`./bin/magento module:enable Feefo_Reviews`
7. Run upgrade scripts of the module:
`./bin/magento setup:upgrade`
8. Flush cache:
`./bin/magento cache:flush`