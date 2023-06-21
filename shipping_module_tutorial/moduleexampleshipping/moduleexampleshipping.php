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

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\ModuleExampleShipping\Entity\ModuleExampleShippingEntity;

class ModuleExampleShipping extends CarrierModule
{
    public function __construct()
    {
        //Required attributes
        $this->name = 'moduleexampleshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
		$this->displayName = $this->trans('Module example Shipping', [], 'Modules.ModuleExampleShipping.Admin');

        //Not required attributes
        $this->description = $this->trans('Description of Module Example Shipping', [], 'Modules.ModuleExampleShipping.Admin');
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => _PS_VERSION_];

        parent::__construct();
        $this->confirmUninstall = $this->l('Are You sure?');
    }

    public function install()
    {
        return parent::install()
        && $this->installDb()
        && $this->installNewCarrier()
        && $this->registerHook('displayAfterCarrier')
        && $this->registerHook('displayAdminOrderSideBottom');
    }
    public function uninstall()
    {
        $id_carrier = Configuration::get('ModuleExampleShippingCarrierId');
        $carrier = new Carrier($id_carrier);
        $carrier->delete();
		
		Configuration::deleteByName('ModuleExampleShippingCarrierId');
        $this->uninstallDb();
        return parent::uninstall();
    }
    private function installDb()
    {
        return Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'meshipping` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `cart_id` varchar(100) NOT NULL,
		  `price` varchar(100) NOT NULL,
		  PRIMARY KEY(`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }
    private function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'meshipping');
    }
	
    public function installNewCarrier()
    {
        $carrier = new Carrier();
        $carrier->name = "Example Carrier";
        $carrier->url = "https://prestashop.com";
        $carrier->delay[Configuration::get('PS_LANG_DEFAULT')] = "2 days";
        $carrier->is_free = false;
        $carrier->active = true;
        $carrier->deleted = false;
        $carrier->shipping_handling = false;
        $carrier->range_behavior = false;
        $carrier->is_module = true;
        $carrier->shipping_external = true;
        $carrier->external_module_name = $this->name;
        $carrier->need_range = true;
        $carrier->max_width = 0;
        $carrier->max_height = 0;
        $carrier->max_depth = 0;
        $carrier->max_weight = 0;
        $carrier->grade = 5;
        if ($carrier->add()) {
            $groupsIds=[];
            foreach(Group::getGroups($this->context->language->id) as $group)
            {
                $groupsIds[]= $group['id_group'];
            }
            $carrier->setGroups($groupsIds);
            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '20';
            $rangeWeight->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '20';
            $rangeWeight->delimiter2 = '40';
            $rangeWeight->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '40';
            $rangeWeight->delimiter2 = '60';
            $rangeWeight->add();

            $zones = Zone::getZones(true);
            foreach ($zones as $zone) {
                $carrier->addZone((int) $zone['id_zone']);
            }

            copy(dirname(__FILE__).'/logo.png',_PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'); 

            Configuration::updateValue('ModuleExampleShippingCarrierId', $carrier->id);
        }
        return true;
    }
    public function hookDisplayAfterCarrier($params)
    {
    	$products=$this->context->cart->getProducts();	
        $this->smarty->assign([
        	'products' => $products
        ]);
        return $this->fetch('module:moduleexampleshipping/views/templates/hook/displayAfterCarrier.tpl');
    }
    public function hookdisplayAdminOrderSideBottom($params)
    {
        $order = new Order((int)$params['id_order']);
		
        if($order && $order->id_cart)
        {
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $moduleExampleShippingRepository = $this->get('prestashop.moduleexampleshipping.repository.moduleexampleshipping_repository');
            $data = $moduleExampleShippingRepository->findOneBy(['cart_id' => $order->id_cart]);
            if($data){
                $this->smarty->assign([
                    'shippingprice' => $data->getPrice()
                ]);
                return $this->fetch('module:moduleexampleshipping/views/templates/hook/displayAdminOrderSideBottom.tpl');
            } else{
                return false;
            }
        }
    }
    public function getOrderShippingCost($params, $shipping_cost)
    {
    	$products=$this->context->cart->getProducts();	
		$cost=0;
		foreach ($products as $product) {
			$cost += ($product['cart_quantity'] ? $product['cart_quantity']*10 : 0); 
		} 
		
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $moduleExampleShippingRepository = $this->get('prestashop.moduleexampleshipping.repository.moduleexampleshipping_repository');

		$id_cart=$this->context->cart->id;
        if($id_cart){
            $data = $moduleExampleShippingRepository->findOneBy(['cart_id'=>$id_cart]);
            if(is_null($data)){
                $newData = new ModuleExampleShippingEntity();
                $newData->setCart($id_cart);
                $newData->setPrice($cost);
                $entityManager->persist($newData);
                $entityManager->flush();
            }else{
                $edition_mode = true;
            }
        }
      return $shipping_cost+$cost;
    }
    public function getOrderShippingCostExternal($params)
    {	
      return 10;
    }
}
