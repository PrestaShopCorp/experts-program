<?php

namespace PrestaShop\ModuleExampleShipping\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ps_meshipping")
 * @ORM\Entity(repositoryClass="PrestaShop\ModuleExampleShipping\Repository\ModuleExampleShippingRepository")
 */
class ModuleExampleShippingEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="cart_id", type="integer")
     */
    private $cart_id;

    /**
     * @var string
     * @ORM\Column(name="price", type="string", length=255)
     */
    private $price;

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
    /**
     * @param int $cart_id
     * @return ModuleExampleShipping
     */
    public function setCart($cart_id)
    {
        $this->cart_id = $cart_id;
        return $this;
    }
    /**
     * @param string $price
     * @return ModuleExampleShipping
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}
