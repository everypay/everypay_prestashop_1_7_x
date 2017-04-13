everypay-prestashop-1.7.x
=========================
Prestashop payment module for version 1.7.x


## Installation instructions

1. Download Zip
2. Rename folder everypay_prestashop_1_7_x-master to everypay_prestashop_1_7_x (inside everypay_prestashop_1_7_x-master.zip)
3. Upload folder everypay_prestashop_1_7_x to /modules directory of Prestashop
or Upload the whole zip through the Admin Panel of Prestashop
4. Install through modules tab of Admin Panel
5. Fill your account details in the module options

## API Keys

1. You can register for a sandbox account ar https://sandbox-dashboard.everypay.gr and then find your API keys under Settings->API Keys
2. For a live account use dashboard.everypay.gr

> Notice: Remember to change the sandbox option in the module to change from testing to production

Installments

You can assign maximum installments in the module Configuration as a single line statement.
#### Format
```total_min:total_max:max_installments;```
e.g.
```45:99.99:3;```
will allow 2 and 3 installments for orders between 45 and 99.99
you can add more rules seperating them by semicolon ;