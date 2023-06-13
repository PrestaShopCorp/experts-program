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

namespace PrestaShop\ModuleExample\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ps_meseconddesc")
 * @ORM\Entity(repositoryClass="PrestaShop\ModuleExample\Repository\ModuleExampleExtensionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ModuleExampleCategoryExtension
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_category", type="integer")
     */
    private $id_category;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer")
     */
    private $id_lang;

    /**
     * @var string
     *
     * @ORM\Column(name="additional_field", type="string", length=255)
     */
    private $additional_field;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id_category;
    }
    /**
     * @return int
     */
    public function getIdCategory()
    {
        return $this->id_category;
    }
    /**
     * @var string
     */
    public function getAdditionalField()
    {
        return $this->additional_field;
    }
    /**
     * @param int $idCategory
     * @return ModuleExampleCategoryExtension
     */
    public function setIdCategory(int $idCategory)
    {
        $this->id_category = $idCategory;
        return $this;
    }
    /**
     * @param int $additionalField
     * @return ModuleExampleCategoryExtension
     */
    public function setAdditionalField($additionalField)
    {
        $this->additional_field = $additionalField;
        return $this;
    }
    /**
     * @param int $idLang
     * @return ModuleExampleCategoryExtension
     */
    public function setIdLang(int $idLang)
    {
        $this->id_lang = $idLang;
        return $this;
    }
}
