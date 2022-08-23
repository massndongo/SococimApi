<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CarteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CarteRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"card"}},
 *     collectionOperations={
 *          "addCard"={
 *              "path"="/card/add",
 *              "method"="POST",
 *              "route_name"="addCard"
 *          },
 *          "getCards"={
 *              "path"="/card/list",
 *              "method"="GET",
 *              "security"="is_granted('ROLE_GCARTE') || is_granted('ROLE_PARTENAIRE')" ,
 *              "security_message"="Access Denied"
 *          },
 *          "reloadCardByCard"={
 *              "path"="/reload/card/{card}",
 *              "method"="POST",
 *              "route_name"="reloadCardByCard"
 *          },
 *          "reloadCardByPhone"={
 *              "path"="/reload/phone/{phone}",
 *              "method"="PUT",
 *              "route_name"="reloadCardByPhone"
 *          }
 *     },
 *     itemOperations={
 *          "getCardById"={
 *              "path"="/card/{id}",
 *              "method"="GET",
 *          },
 *          "blockCard"={
 *              "path"="/card/{id}/block",
 *              "method"="PUT",
 *              "route_name"="blockCard"
 *          },
 *          "unblockCard"={
 *              "path"="/card/{id}/unblock",
 *              "method"="PUT",
 *              "route_name"="unblockCard"
 *          }
 *     }
 * )
 */
class Carte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"card"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card"})
     */
    private $idCarte;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card"})
     */
    private $numCarte;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card"})
     */
    private $solde = 0;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="carte", cascade={"persist", "remove"})
     * @Groups({"card"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"card"})
     */
    private $blocked = false;

    /**
     * @ORM\OneToMany(targetEntity=Pointing::class, mappedBy="cartes")
     */
    private $pointings;

    public function __construct()
    {
        $this->pointings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCarte(): ?string
    {
        return $this->idCarte;
    }

    public function setIdCarte(string $idCarte): self
    {
        $this->idCarte = $idCarte;

        return $this;
    }

    public function getNumCarte(): ?string
    {
        return $this->numCarte;
    }

    public function setNumCarte(string $numCarte): self
    {
        $this->numCarte = $numCarte;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
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
     * @return Collection|Pointing[]
     */
    public function getPointings(): Collection
    {
        return $this->pointings;
    }

    public function addPointing(Pointing $pointing): self
    {
        if (!$this->pointings->contains($pointing)) {
            $this->pointings[] = $pointing;
            $pointing->setCarte($this);
        }

        return $this;
    }

    public function removePointing(Pointing $pointing): self
    {
        if ($this->pointings->removeElement($pointing)) {
            // set the owning side to null (unless already changed)
            if ($pointing->getCarte() === $this) {
                $pointing->setCarte(null);
            }
        }

        return $this;
    }
}
