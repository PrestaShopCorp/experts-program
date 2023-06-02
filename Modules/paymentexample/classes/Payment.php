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
    public function getOfflinePaymentOption()
    {
        $result = new PaymentOption();
        $result->setCallToActionText($this->trans('Pay offline', [], 'Modules.Paymentexample.Payment'))
            ->setAction($this->context->link->getModuleLink($this->module->name, 'validation', [], true))
            ->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/frontend/info.tpl'))
            ->setLogo(\Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name . '/payment.png'));

        return $result;
    }

    // public function getExternalPaymentOption()
    // {
    //     $externalOption = new PaymentOption();
    //     $externalOption->setCallToActionText($this->l('Pay external'))
    //                    ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
    //                    ->setInputs([
    //                         'token' => [
    //                             'name' =>'token',
    //                             'type' =>'hidden',
    //                             'value' =>'123456789',
    //                         ],
    //                     ])
    //                    ->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/front/payment_infos.tpl'))
    //                    ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.png'));

    //     return $externalOption;
    // }

    // public function getEmbeddedPaymentOption()
    // {
    //     $embeddedOption = new PaymentOption();
    //     $embeddedOption->setCallToActionText($this->l('Pay embedded'))
    //                    ->setForm($this->generateForm())
    //                    ->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/front/payment_infos.tpl'))
    //                    ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.png'));

    //     return $embeddedOption;
    // }

    // public function getIframePaymentOption()
    // {
    //     $iframeOption = new PaymentOption();
    //     $iframeOption->setCallToActionText($this->l('Pay iframe'))
    //                  ->setAction($this->context->link->getModuleLink($this->name, 'iframe', array(), true))
    //                  ->setAdditionalInformation($this->context->smarty->fetch('module:paymentexample/views/templates/front/payment_infos.tpl'))
    //                  ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.png'));

    //     return $iframeOption;
    // }

    // protected function generateForm()
    // {
    //     $months = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $months[] = sprintf("%02d", $i);
    //     }

    //     $years = [];
    //     for ($i = 0; $i <= 10; $i++) {
    //         $years[] = date('Y', strtotime('+'.$i.' years'));
    //     }

    //     $this->context->smarty->assign([
    //         'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
    //         'months' => $months,
    //         'years' => $years,
    //     ]);

    //     return $this->context->smarty->fetch('module:paymentexample/views/templates/front/payment_form.tpl');
    // }
}