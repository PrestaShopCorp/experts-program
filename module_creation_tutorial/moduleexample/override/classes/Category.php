<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */


class Category extends CategoryCore
{
    public function getSubCategories($idLang, $active = true)
    {
        $sqlGroupsWhere = '';
        $sqlGroupsJoin = '';
        if (Group::isFeatureActive()) {
            $sqlGroupsJoin = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cg.`id_category` = c.`id_category`)';
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroupsWhere = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.`id_lang`, cl.`name`, cl.`description`, cl.`additional_description`, cl.`link_rewrite`, cl.`meta_title`, cl.`meta_keywords`, cl.`meta_description`
		FROM `' . _DB_PREFIX_ . 'category` c
		' . Shop::addSqlAssociation('category', 'c') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int) $idLang . ' ' . Shop::addSqlRestrictionOnLang('cl') . ')
		' . $sqlGroupsJoin . '
		WHERE `id_parent` = ' . (int) $this->id . '
		' . ($active ? 'AND `active` = 1' : '') . '
		' . $sqlGroupsWhere . '
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, category_shop.`position` ASC');

        foreach ($result as &$row) {
            $row['id_image'] = Tools::file_exists_cache($this->image_dir . $row['id_category'] . '.jpg') ? (int) $row['id_category'] : Language::getIsoById($idLang) . '-default';
            $row['legend'] = 'no picture';
			$row['name'] = 'NAME OVERRIDE';
        }
        return $result;
    }
}
