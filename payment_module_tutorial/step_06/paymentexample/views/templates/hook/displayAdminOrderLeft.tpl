{**
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
 *}

<section id="{$moduleName}-displayAdminOrderLeft">
  <div class="panel">
    <div class="panel-heading">
      <img src="{$moduleLogoSrc}" alt="{$moduleDisplayName}" width="15" height="15">
      {$moduleDisplayName}
    </div>
    <p>{l s='This order has been paid with %moduleDisplayName%.' d='Modules.Paymentexample.DisplayAdminOrderLeft' sprintf=['%moduleDisplayName%' => $moduleDisplayName]}</p>
  </div>
</section>
