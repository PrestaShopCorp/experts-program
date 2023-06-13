# Example payment module

## STEP #3 - External Payment
This step add a new payment method introducing more concepts about creating a payment module like transaction ID.

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