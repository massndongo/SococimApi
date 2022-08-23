<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FoodPointingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FoodPointingRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"foodPointing"}},
 *     collectionOperations={
 *          "getFoodPointing"={"path"="/foodPointing/list", "method"="GET"},
 *          "addFoodPointing"={"path"="/foodPointing/add", "method"="POST", "route_name"="addFoodPointing"},
 *     }
 * )
 */
class FoodPointing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"foodPointing"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="foodPointings")
     * @Groups({"foodPointing"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="foodPointings")
     * @Groups({"foodPointing"})
     */
    private $menu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $pointDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

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

    public function getPointDate(): ?\DateTimeInterface
    {
        return $this->pointDate;
    }

    public function setPointDate(\DateTimeInterface $pointDate): self
    {
        $this->pointDate = $pointDate;

        return $this;
    }
}
