# Example payment module

## STEP #6 - Cancel a transaction
This step adds the ability to cancel a transaction.

### Installation
Before you start using this module, you need to generate the composer files with `composer dump-autoload` for the first time inside the `paymentexample` folder.

### Included features
- Payment Module Main Structure
- Basic hooks for a payment module
- Validator controller
- Offline payment option
- Localization based on the new PrestaShop translation system
- New order status: creation and removal when module is uninstalled
- Email template (English version) to send with the new order status.