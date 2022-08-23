<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_ACCEPTEUR')",
 *     securityMessage="Access Denied",
 *     collectionOperations={
 *          "addMenu"={"path"="/menu/add", "method"="POST"},
 *          "getMenus"={"path"="/menu/list", "method"="GET"},
 *     },
 *     itemOperations={
 *          "getMenuById"={"path"="/menu/{id}", "method"="GET"},
 *          "updateMenu"={"path"="/menu/{id}", "method"="PUT"}
 *     }
 * )
 */
class Menu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"foodPointing"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"foodPointing"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"foodPointing"})
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked = false;

    /**
     * @ORM\OneToMany(targetEntity=FoodPointing::class, mappedBy="menu")
     */
    private $foodPointings;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function __construct()
    {
        $this->foodPointings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return Collection|FoodPointing[]
     */
    public function getFoodPointings(): Collection
    {
        return $this->foodPointings;
    }

    public function addFoodPointing(FoodPointing $foodPointing): self
    {
        if (!$this->foodPointings->contains($foodPointing)) {
            $this->foodPointings[] = $foodPointing;
            $foodPointing->setMenu($this);
        }

        return $this;
    }

    public function removeFoodPointing(FoodPointing $foodPointing): self
    {
        if ($this->foodPointings->removeElement($foodPointing)) {
            // set the owning side to null (unless already changed)
            if ($foodPointing->getMenu() === $this) {
                $foodPointing->setMenu(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
