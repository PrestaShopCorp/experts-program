<?php
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
