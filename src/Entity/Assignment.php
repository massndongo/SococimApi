<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AssignmentRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_GACCES')",
 *     securityMessage="Access Denied",
 *     normalizationContext={"groups"={"assignment"}},
 *     denormalizationContext={"groups"={"assignmentAdd"}},
 *     collectionOperations={
 *          "getAssignments"={"path"="/assignment/list", "method"="GET"},
 *          "addAssignment"={"path"="/assign/controleur", "method"="POST", "route_name"="assignControleur"}
 *     }
 * )
 */
class Assignment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"checkpoint","assignment"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assignments")
     * @Groups({"checkpoint","assignment","assignmentAdd"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Checkpoint::class, inversedBy="assignments")
     * @Groups({"assignment","assignmentAdd"})
     */
    private $checkpoint;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"checkpoint","assignment","assignmentAdd"})
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"checkpoint","assignment","assignmentAdd"})
     */
    private $dateFin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked = false;

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

    public function getCheckpoint(): ?Checkpoint
    {
        return $this->checkpoint;
    }

    public function setCheckpoint(?Checkpoint $checkpoint): self
    {
        $this->checkpoint = $checkpoint;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

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
}
