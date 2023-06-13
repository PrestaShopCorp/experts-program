<?php
/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\Module\PaymentExample;

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class Payment extends AbstractPayment
{
    /**
     * Factory of PaymentOption for Offline Payment
     *
     * @return PaymentOption
     */
    public function getOfflinePaymentOption()
    {
        $po = new PaymentOption();
        $po->setModuleName($this->module->name);
        $po->setCallToActionText($this->trans('Pay offline', [], 'Modules.Paymentexample.Payment'));
        $po->setAction($this->context->link->getModuleLink($this->module->name, 'validation', ['option' => 'offline'], true));
        $po->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/front/paymentOptionOffline.tpl'));
        $po->setLogo(\Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/option/offline.png'));

        return $po;
    }

    /**
     * Factory of PaymentOption for External Payment
     *
     * @return PaymentOption
     */
    public function getExternalPaymentOption()
    {
        $po = new PaymentOption();
        $po->setModuleName($this->module->name);
        $po->setCallToActionText($this->trans('Pay external', [], 'Modules.Paymentexample.Payment'));
        $po->setAction($this->context->link->getModuleLink($this->module->name, 'external', [], true));
        $po->setInputs([
            'token' => [
                'name' => 'token',
                'type' => 'hidden',
                'value' => '[5cbfniD+(gEV<59lYbG/,3VmHiE<U46;#G9*#NP#X.FA§]sb%ZG?5Q{xQ4#VM|7',
            ],
        ]);
        $po->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/front/paymentOptionExternal.tpl'));
        $po->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/option/external.png'));

        return $po;
    }
}