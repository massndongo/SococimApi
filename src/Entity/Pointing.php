<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PointingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PointingRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_CONTROLEUR')",
 *     securityMessage="Acces Denied",
 *     normalizationContext={"groups"={"pointing"}},
 *     collectionOperations={
 *          "employePointing"={"path"="/employe/{carte}/pointing", "method"="POST", "route_name"="employePointing"},
 *     },
 * )
 */
class Pointing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Carte::class, inversedBy="pointings")
     */
    private $carte;

    /**
     * @ORM\ManyToOne(targetEntity=Checkpoint::class, inversedBy="pointings")
     */
    private $checkpoint;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEntree;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateSortie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pointings")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarte(): ?Carte
    {
        return $this->carte;
    }

    public function setCarte(?Carte $carte): self
    {
        $this->carte = $carte;

        return $this;
    }

    public function getCheckpoint(): ?Checkpoint
    {
        return $this->checkpoint;
    }

    public function setCheckpoint(?Checkpoint $checkpoint): self
    {
        $this->checkpoint = $checkpoint;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTimeInterface $dateEntree): self
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

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
}
