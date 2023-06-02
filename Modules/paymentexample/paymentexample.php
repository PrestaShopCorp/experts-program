<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class PaymentExample extends PaymentModule
{
    // >>>> Main settings <<<<
    // const CONFIG_OS_OFFLINE = 'PAYMENTEXAMPLE_OS_OFFLINE';
    
    // const CONFIG_PO_OFFLINE_ENABLED = 'PAYMENTEXAMPLE_PO_OFFLINE_ENABLED';
    // const CONFIG_PO_EXTERNAL_ENABLED = 'PAYMENTEXAMPLE_PO_EXTERNAL_ENABLED';
    // const CONFIG_PO_EMBEDDED_ENABLED = 'PAYMENTEXAMPLE_PO_EMBEDDED_ENABLED';
    // const CONFIG_PO_BINARY_ENABLED = 'PAYMENTEXAMPLE_PO_BINARY_ENABLED';
    
    // const MODULE_ADMIN_CONTROLLER = 'AdminConfigurePaymentExample';
    
    const HOOKS = [
        // 'actionPaymentCCAdd',
        'actionObjectShopAddAfter',
        'paymentOptions',
        'displayAdminOrderLeft',
        'displayAdminOrderMainBottom',
        'displayCustomerAccount',
        // 'displayOrderConfirmation',
        // 'displayOrderDetail',
        // 'displayPaymentByBinaries',
        'displayPaymentReturn',
        'displayPDFInvoice',
    ];

    public function isUsingNewTranslationSystem() {
		return true;
	}

    protected $_html = '';
    protected $_postErrors = [];

    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'paymentexample';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->ps_versions_compliancy = [
            'min' => '1.7.6',
            'max' => '8.99.99'
        ];

        $this->bootstrap = true;
        $this->controllers = ['validation'];
        
        parent::__construct();

        $this->displayName = $this->trans('Payment Example', [], 'Modules.Paymentexample.Admin');
        $this->description = $this->trans('Description of Payment Example', [], 'Modules.Paymentexample.Admin');

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->trans('No currency has been set for this module.', [], 'Modules.Paymentexample.Admin');
        }
    }

    /**
     * @return bool
     */
    public function install()
    {
        return (bool) parent::install()
            && (bool) $this->registerHook(static::HOOKS)
            // && $this->installOrderState()
            // && $this->installConfiguration()
            // && $this->installTabs()
        ;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return (bool) parent::uninstall()
            // && $this->deleteOrderState()
            // && $this->uninstallConfiguration()
            // && $this->uninstallTabs()
        ;
    }

    // public function getContent()
    // {
    //     $p = new PaymentOptions();
    //     $p::test();
    // }

    // >>>> END Main settings <<<<

	// >>>> Hooks <<<<

    /**
     * This hook called after a new Shop is created
     *
     * @param array $params
     */
    public function hookActionObjectShopAddAfter(array $params)
    {
        if (empty($params['object'])) {
            return;
        }

        /** @var Shop $shop */
        $shop = $params['object'];

        if (false === Validate::isLoadedObject($shop)) {
            return;
        }

        $this->addCheckboxCarrierRestrictionsForModule([(int) $shop->id]);
        $this->addCheckboxCountryRestrictionsForModule([(int) $shop->id]);

        if ($this->currencies_mode === 'checkbox') {
            $this->addCheckboxCurrencyRestrictionsForModule([(int) $shop->id]);
        } elseif ($this->currencies_mode === 'radio') {
            $this->addRadioCurrencyRestrictionsForModule([(int) $shop->id]);
        }
    }

    /**
     * @param array $params
     *
     * @return array Should always return an array
     */
    public function hookPaymentOptions($params)
    {
        /** @var Cart $cart */
        $cart = $params['cart'];

        if (false === Validate::isLoadedObject($cart) || false === $this->checkCurrency($cart)) {
            return [];
        }

        $payment = new PrestaShop\Module\PaymentExample\Payment($this);
        $paymentOptions = [];

        // if (Configuration::get(static::CONFIG_PO_OFFLINE_ENABLED)) {
            $paymentOptions[] = $payment->getOfflinePaymentOption();
        // }

        /*
        if (Configuration::get(static::CONFIG_PO_EXTERNAL_ENABLED)) {
            $paymentOptions[] = $this->getExternalPaymentOption();
        }

        if (Configuration::get(static::CONFIG_PO_EMBEDDED_ENABLED)) {
            $paymentOptions[] = $this->getEmbeddedPaymentOption();
        }

        if (Configuration::get(static::CONFIG_PO_BINARY_ENABLED)) {
            $paymentOptions[] = $this->getBinaryPaymentOption();
        }
        */

        return $paymentOptions;
    }

    // public function hookPaymentReturn()
    // {
    //     return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/frontend/return.tpl');
    // }


    /**
     * This hook is used to display additional information on BO Order View, under Payment block
     *
     * @since PrestaShop 1.7.7 This hook is replaced by displayAdminOrderMainBottom on migrated BO Order View
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminOrderLeft(array $params)
    {
        if (empty($params['id_order'])) {
            return '';
        }

        $order = new Order((int) $params['id_order']);

        if (false === Validate::isLoadedObject($order) || $order->module !== $this->name) {
            return '';
        }

        $this->context->smarty->assign([
            'moduleName' => $this->name,
            'moduleDisplayName' => $this->displayName,
            'moduleLogoSrc' => $this->getPathUri() . 'logo.png',
        ]);

        return $this->context->smarty->fetch('module:paymentexample/views/templates/hook/displayAdminOrderLeft.tpl');
    }

    /**
     * This hook is used to display additional information on BO Order View, under Payment block
     *
     * @since PrestaShop 1.7.7 This hook replace displayAdminOrderLeft on migrated BO Order View
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayAdminOrderMainBottom(array $params)
    {
        if (empty($params['id_order'])) {
            return '';
        }

        $order = new Order((int) $params['id_order']);

        if (false === Validate::isLoadedObject($order) || $order->module !== $this->name) {
            return '';
        }

        $this->context->smarty->assign([
            'moduleName' => $this->name,
            'moduleDisplayName' => $this->displayName,
            'moduleLogoSrc' => $this->getPathUri() . 'logo.png',
        ]);

        return $this->context->smarty->fetch('module:paymentexample/views/templates/hook/displayAdminOrderMainBottom.tpl');
    }

    /**
     * This hook is used to display information in customer account
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayCustomerAccount(array $params)
    {
        $this->context->smarty->assign([
            'moduleDisplayName' => $this->displayName,
            'moduleLogoSrc' => $this->getPathUri() . 'logo.png',
            'transactionsLink' => $this->context->link->getModuleLink(
                $this->name,
                'account'
            ),
        ]);

        return $this->context->smarty->fetch('module:paymentexample/views/templates/hook/displayCustomerAccount.tpl');
    }

    /**
     * This hook is used to display additional information on bottom of order confirmation page
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayPaymentReturn(array $params)
    {
        if (empty($params['order'])) {
            return '';
        }

        /** @var Order $order */
        $order = $params['order'];

        if (false === Validate::isLoadedObject($order) || $order->module !== $this->name) {
            return '';
        }

        $transaction = '';

        if ($order->getOrderPaymentCollection()->count()) {
            /** @var OrderPayment $orderPayment */
            $orderPayment = $order->getOrderPaymentCollection()->getFirst();
            $transaction = $orderPayment->transaction_id;
        }

        $this->context->smarty->assign([
            'moduleName' => $this->name,
            'transaction' => $transaction,
            'transactionsLink' => $this->context->link->getModuleLink(
                $this->name,
                'account'
            ),
        ]);

        return $this->context->smarty->fetch('module:paymentexample/views/templates/hook/displayPaymentReturn.tpl');
    }

    /**
     * This hook is used to display additional information on Invoice PDF
     *
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayPDFInvoice(array $params)
    {
        if (empty($params['object'])) {
            return '';
        }

        /** @var OrderInvoice $orderInvoice */
        $orderInvoice = $params['object'];

        if (false === Validate::isLoadedObject($orderInvoice)) {
            return '';
        }

        $order = $orderInvoice->getOrder();

        if (false === Validate::isLoadedObject($order) || $order->module !== $this->name) {
            return '';
        }

        $transaction = '';

        if ($order->getOrderPaymentCollection()->count()) {
            /** @var OrderPayment $orderPayment */
            $orderPayment = $order->getOrderPaymentCollection()->getFirst();
            $transaction = $orderPayment->transaction_id;
        }

        $this->context->smarty->assign([
            'moduleName' => $this->name,
            'transaction' => $transaction,
        ]);

        return $this->context->smarty->fetch('module:paymentexample/views/templates/hook/displayPDFInvoice.tpl');
    }

    // >>>> END Hooks <<<<

    // >>>> Internal functionallity <<<<
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }
    // >>>> END Internal functionallity <<<<
}
