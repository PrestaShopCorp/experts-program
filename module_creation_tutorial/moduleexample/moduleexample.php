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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\ModuleExample\Controller\Admin\ModuleExampleConfigureController;
use PrestaShop\ModuleExample\Domain\Command\AddExtensionCommand;
use PrestaShop\ModuleExample\Domain\Command\EditExtensionCommand;
use PrestaShop\ModuleExample\Entity\ModuleExampleCategoryExtension;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ModuleExample extends Module implements WidgetInterface
{
    public function __construct()
    {
        //Required attributes
        $this->name = 'moduleexample';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
		$this->displayName = $this->trans('Module example', [], 'Modules.ModuleExample.Admin');

        //Not required attributes
        $this->description = $this->trans('Description of Module Example', [], 'Modules.ModuleExample.Admin');
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $tabNames = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Module example Configure', [], 'Modules.ModuleExample.Admin', $lang['locale']);
        }
        $tabNamesForm = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNamesForm[$lang['locale']] = $this->trans('Module example Form', [], 'Modules.ModuleExample.Admin', $lang['locale']);
        }
        $tabNamesGrid = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNamesGrid[$lang['locale']] = $this->trans('Module example Grid', [], 'Modules.ModuleExample.Admin', $lang['locale']);
        }
        $this->tabs = [
            [
                'route_name' => 'ps_controller_tabs_moduleexampleconfigure',
                'class_name' => 'AdminModuleExampleControllerTabsConfigure',
                'visible' => true,
                'name' => $tabNames,
                'icon' => 'school',
                'parent_class_name' => 'IMPROVE',
            ],
            [
                'route_name' => 'ps_controller_form_moduleexample',
                'class_name' => 'AdminModuleExampleControllerForm',
                'visible' => true,
                'name' => $tabNamesForm,
                'icon' => 'school',
                'parent_class_name' => 'IMPROVE',
            ],
            [
                'route_name' => 'demo_grid_index_moduleexample',
                'class_name' => 'AdminModuleExampleDemoGrid',
                'visible' => true,
                'name' => $tabNamesGrid,
                'icon' => 'school',
                'parent_class_name' => 'IMPROVE',
            ],
        ];
        parent::__construct();
    }
    /**
     * @return bool
     */
    public function install(): bool
    {
        return parent::install() 
		&& $this->installDb()		
        && $this->registerHook('displayHome') 
        && $this->registerHook('displayFooter') 
        && $this->registerHook('actionFrontControllerSetMedia')
		&& $this->registerHook('actionCategoryFormBuilderModifier')
		&& $this->registerHook('actionAfterCreateCategoryFormHandler')
		&& $this->registerHook('actionAfterUpdateCategoryFormHandler');
    }
    /**
     * @return bool
     */
    public function uninstall()
    {
    	$this->uninstallDb();
        return parent::uninstall();
    }
    public function hookDisplayHome($params)
    {
		// Set Variable
        Configuration::updatevalue('moduleexampleConfig','1');
		
		// Get Variable
        $moduleexampleConfig=Configuration::get('moduleexampleConfig');
		
		// Example service test
        $service = $this->get('presta_shop.module_example.module_example_service');
        $upperstring=$service->dostrtoupper('Hello!');

		// Assign to template
        $this->context->smarty->assign([
            'moduleexampleConfig' => $moduleexampleConfig
        ]);

		// Render template
		return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/hook/moduleexample.tpl');
    }
    public function hookactionFrontControllerSetMedia($params)
    {
    	// Register stylesheet
        $this->context->controller->registerStylesheet(
            'moduleexample-style',
            'modules/'.$this->name.'/views/css/style.css',
            [
            'media' => 'all',
            'priority' => 200,
            ]
        );
		
		// Register javascript
        $this->context->controller->registerJavascript(
            'moduleexample-script',
            'modules/'.$this->name.'/views/js/script.js',
            [
              'position' => 'footer',
              'inline' => true,
              'priority' => 10,
            ]
        );
    }
    public function hookActionCategoryFormBuilderModifier($params)
    {
        $formBuilder = $params['form_builder'];
        $formBuilder->add('additional_field', TranslatableType::class, [
            'type' => TextareaType::class,
            'label' => $this->trans('Category extension', [], 'Modules.ModuleExample.Admin'),
            'required' => false,
        ]);
		if(isset($params['id']) && $params['id']>0){
            $entityManager = $this->get('doctrine.orm.entity_manager');
			$extensionRepository = $entityManager->getRepository(ModuleExampleCategoryExtension::class);
	        $languages = Language::getLanguages(false);
	        $id_category = $params['id'];

			$additional_field = [];
	        foreach ($languages as $lang) {
	        	$id_lang = $lang['id_lang'];
	        	$extension = $extensionRepository->findOneBy(['id_category' => $id_category,'id_lang' => $id_lang]);
				 if(!is_null($extension)){
					$additionalField=$extension->getAdditionalField();
					$additional_field[$id_lang]=$additionalField;
				 }
			}
			$params['data']['additional_field']=$additional_field;
		}
        $formBuilder->setData($params['data']);
    }
    public function hookActionAfterCreateCategoryFormHandler($params)
    {
        $this->updateCategoryExtension($params);
    }
    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
    	$this->updateCategoryExtension($params);
	}
    public function updateCategoryExtension($params)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $extensionRepository = $entityManager->getRepository(ModuleExampleCategoryExtension::class);
		$id_category = $params['id'];
		$additional_field = $params['form_data']['additional_field'];
		$extension = $extensionRepository->findOneBy(['id_category'=>(int)$id_category]);

		if(is_null($extension)){
			foreach ($additional_field as $key => $additionalLangField) {
		        try{
		            $extension = new ModuleExampleCategoryExtension();
		            $extension->setIdCategory($id_category);
					$extension->setAdditionalField($additionalLangField);
					$extension->setIdLang($key);
					$entityManager->persist($extension);
			        $entityManager->flush();	
		        }
		        catch(\Exception $e){
		                sprintf('Failed to add the extension');
		        }
			}
		}
		else {
			foreach ($additional_field as $key => $additionalLangField) {
				try{
					$extension = $extensionRepository->findOneBy(['id_category' => $id_category,'id_lang' => $key]);
					$extension->setAdditionalField($additionalLangField);
					$entityManager->persist($extension);
			        $entityManager->flush();	
		        }
		        catch(\Exception $e){
		                sprintf('Failed to modify extension');
		        }
			}
		}
    }
	
    public function getContent()
    {
        // >>> You can uncomment to see how it works <<<

        // >>> Legacy HelperForm class form generation <<<
        $contentsave = $this->postProcessRuleSave();
        return $contentsave.$this->displayForm().$this->displayList();

        // >>> Modern way redirection <<<
        //Tools::redirectAdmin(
        //    $this->context->link->getAdminLink(ConfigureController::TAB_CLASS_NAME)
        //);

        // >>> Legacy tpl file <<<
        //return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/config.tpl');
    }
	
    public function renderWidget($hookName, array $configuration)
    {
        //Widget Example
    	$this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        if ($hookName === 'displayFooter') {
        	return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/hook/moduleexamplefooter.tpl');
        }
		return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/hook/moduleexample.tpl');
    }
    public function getWidgetVariables($hookName, array $configuration)
    {
        //Widget variables
        return [
            'my_var1' => 'my_var1_value',
            'my_var2' => 'my_var2_value',
            'my_var_n' => 'my_var_n_value'
        ];
    }
    protected function postProcessRuleSave()
    {
        //Legacy form configuration save
        $output = '';
        if (Tools::isSubmit('submitModuleExample')) {
            Configuration::updateValue('moduleexample_check', Tools::getValue('moduleexample_check'));
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $id_lang = $language['id_lang'];
                $var1 = Tools::getValue('moduleexample_text_' . $id_lang);
                Configuration::updateValue('moduleexample_text_' . $id_lang, $var1);
            }
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        } 
        return $output;
    }
    protected function displayForm()
    {
        //Legacy HelperForm generation
        $token = Tools::getAdminToken($this->name);
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                      'type' => 'text',
                      'lang' => true,
                      'label' => $this->l('Text input Demo'),
                      'name' => 'moduleexample_text',
                      'desc' => 'Description',
                    ],
                    [
                      'type' => 'switch',
                      'label' => $this->l('Checkbox'),
                      'name' => 'moduleexample_check',
                      'desc' => $this->l('Checkbox Demo'),
                      'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                      ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        
        $helper->submit_action = 'submitModuleExample';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $fields = [];
        $fields['moduleexample_check'] = Configuration::get('moduleexample_check');
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $id_lang = $language['id_lang'];
            $fields['moduleexample_text'][$id_lang] = Configuration::get('moduleexample_text_' . $language['id_lang']);
        }
        $helper->tpl_vars = [
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
        ];

        return $helper->generateForm([$fields_form]);
    }
	protected function displayList()
	{
		$list_data= 
	    $fields_list = array(
	        'id' => array(
	            'title' => $this->l('Id'),
	            'width' => 140,
	            'type' => 'text',
	        ),
	        'firstname' => array(
	            'title' => $this->l('Name'),
	            'width' => 140,
	            'type' => 'text',
	        ),
	        'lastname' => array(
	            'title' => $this->l('Name'),
	            'width' => 140,
	            'type' => 'text',
	        ),
	    );
	    $helper = new HelperList();
	     
	    $helper->shopLinkType = '';
	     
	    $helper->simple_header = true;
	     
	    // Actions to be displayed in the "Actions" column
	    $helper->actions = array('edit', 'delete', 'view');
	     
	    $helper->identifier = 'id_category';
	    $helper->show_toolbar = true;
	    $helper->title = 'HelperList';
	    $helper->table = $this->name.'_categories';
	     
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$subscribers = $this->getSubscribers();
	    return $helper->generateList($subscribers,$fields_list);
	}
    public function getSubscribers()
    {
        $dbquery = new DbQuery();
        $dbquery->select('c.`id_customer` AS `id`, s.`name` AS `shop_name`, gl.`name` AS `gender`, c.`lastname`, c.`firstname`, c.`email`, c.`newsletter` AS `subscribed`, c.`newsletter_date_add`, l.`iso_code`');
        $dbquery->from('customer', 'c');
        $dbquery->leftJoin('shop', 's', 's.id_shop = c.id_shop');
        $dbquery->leftJoin('gender', 'g', 'g.id_gender = c.id_gender');
        $dbquery->leftJoin('gender_lang', 'gl', 'g.id_gender = gl.id_gender AND gl.id_lang = ' . (int) $this->context->employee->id_lang);
        $dbquery->where('c.`newsletter` = 1');
        $dbquery->leftJoin('lang', 'l', 'l.id_lang = c.id_lang');

        $subscribers = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS($dbquery->build());
		
        return $subscribers;
    }
    private function installDb()
    {
        return Db::getInstance()->execute('
	        CREATE TABLE `' . _DB_PREFIX_ . 'meseconddesc` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `id_category` int(10) UNSIGNED DEFAULT NULL,
			  `id_lang` int(10) UNSIGNED DEFAULT NULL,
			  `additional_field` text DEFAULT NULL,
			  PRIMARY KEY(`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    }

    private function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'meseconddesc');
    }
}