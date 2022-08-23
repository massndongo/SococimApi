<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CheckpointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CheckpointRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_GACCES')",
 *     securityMessage="Acces Denied",
 *     normalizationContext={"groups"={"checkpoint"}},
 *     collectionOperations={
 *          "addCheckpoint"={"path"="/checkpoint/add", "method"="POST"},
 *          "getCheckpoint"={"path"="/checkpoint/list", "method"="GET"},
 *          "getBlockedCheckpoint"={"path"="/checkpoint/blocked", "method"="GET", "route_name"="getBlockedCheckpoint"},
 *     },
 *     itemOperations={
 *          "getCheckpointById"={"path"="/checkpoint/{id}/list", "method"="GET"},
 *          "blockCheckpoint"={"path"="/checkpoint/{id}/block", "method"="PUT", "route_name"="blockCheckpoint"},
 *          "unblockCheckpoint"={"path"="/checkpoint/{id}/unblock", "method"="PUT", "route_name"="unblockCheckpoint"},
 *     }
 * )
 * @UniqueEntity("libelle")
 */
class Checkpoint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"checkpoint","assignment"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"checkpoint","assignment"})
     * @Assert\NotBlank
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"checkpoint","assignment"})
     */
    private $blocked = false;

    /**
     * @ORM\OneToMany(targetEntity=Assignment::class, mappedBy="checkpoint")
     * @Groups({"checkpoint"})
     */
    private $assignments;

    /**
     * @ORM\OneToMany(targetEntity=Pointing::class, mappedBy="checkpoints")
     */
    private $pointings;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
        $this->pointings = new ArrayCollection();
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
     * @return Collection|Assignment[]
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): self
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments[] = $assignment;
            $assignment->setCheckpoint($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): self
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getCheckpoint() === $this) {
                $assignment->setCheckpoint(null);
            }
        }

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
            $pointing->setCheckpoints($this);
        }

        return $this;
    }

    public function removePointing(Pointing $pointing): self
    {
        if ($this->pointings->removeElement($pointing)) {
            // set the owning side to null (unless already changed)
            if ($pointing->getCheckpoints() === $this) {
                $pointing->setCheckpoints(null);
            }
        }

        return $this;
    }
}
