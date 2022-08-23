<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *     security="is_granted('ROLE_PARTENAIRE')",
 *     securityMessage="Acces denied ! Only Partenaire has rights !",
 *     collectionOperations={
 *          "addUser"={
 *              "path"="/user/add",
 *              "method"="POST",
 *              "route_name"="addUser"
 *          },
 *          "getGCarte"={
 *              "path"="/gcarte/list",
 *              "method"="GET",
 *              "route_name"="getGCarte"
 *          },
 *          "getGAcces"={
 *              "path"="/gacces/list",
 *              "method"="GET",
 *              "route_name"="getGAcces"
 *          },
 *          "getAccepteur"={
 *              "path"="/accepteur/list",
 *              "method"="GET",
 *              "route_name"="getAccepteur"
 *          },
 *          "getControleur"={
 *              "path"="/controleur/list",
 *              "method"="GET",
 *              "route_name"="getControleur"
 *          },
 *          "getUser"={
 *              "path"="/user/list",
 *              "method"="GET"
 *          }
 *     },
 *     itemOperations={
 *          "get"={"path"="/user/{id}"},
 *          "getEmployeById"={
 *              "path"="/employe/{id}/list",
 *              "method"="GET",
 *              "route_name"="getEmployeById"
 *          },
 *          "blockUser"={
 *              "path"="/user/{id}/block",
 *              "method"="PUT",
 *              "route_name"="blockUser"
 *          },
 *          "unblockUser"={
 *              "path"="/user/{id}/unblock",
 *              "method"="PUT",
 *              "route_name"="unblockUser"
 *          },
 *          "profil"={
 *             "path"="/profil/data",
 *              "method"="GET",
 *              "normalization_context"={"groups"={"read"}}
 *          }
 *     }
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"card","assignment","foodPointing"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card","assignment","foodPointing"})
     */
    private $mat;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"card","assignment","foodPointing"})
     * @Assert\NotBlank
     */
    private $email;

    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card","assignment","foodPointing"})
     * @Assert\NotBlank
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card","assignment","foodPointing"})
     * @Assert\NotBlank
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card","assignment","foodPointing"})
     * @Assert\NotBlank
     */
    private $adresse;

    /**
     * @ORM\Column(type="blob")
     * @Groups({"card","assignment","foodPointing"})
     * @Assert\NotBlank
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private $profil;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"card"})
     */
    private $blocked = false;

    /**
     * @ORM\OneToOne(targetEntity=Carte::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $carte;

    /**
     * @ORM\ManyToOne(targetEntity=Checkpoint::class, inversedBy="users")
     */
    private $checkpoint;

    /**
     * @ORM\OneToOne(targetEntity=Assignment::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $assignment;

    /**
     * @ORM\OneToMany(targetEntity=Assignment::class, mappedBy="user")
     */
    private $assignments;

    /**
     * @ORM\OneToMany(targetEntity=Pointing::class, mappedBy="user")
     */
    private $pointings;

    /**
     * @ORM\OneToMany(targetEntity=FoodPointing::class, mappedBy="user")
     */
    private $foodPointings;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
        $this->pointings = new ArrayCollection();
        $this->foodPointings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMat(): ?string
    {
        return $this->mat;
    }

    public function setMat(string $mat): self
    {
        $this->mat = $mat;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        return ["ROLE_".$this->profil->getLibelle()];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAvatar()
    {
        if ($this->avatar) {
            $avatar_str = stream_get_contents($this->avatar);
            return base64_encode($avatar_str);
        }
        return null;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

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

    public function getCarte(): ?Carte
    {
        return $this->carte;
    }

    public function setCarte(?Carte $carte): self
    {
        // unset the owning side of the relation if necessary
        if ($carte === null && $this->carte !== null) {
            $this->carte->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($carte !== null && $carte->getUser() !== $this) {
            $carte->setUser($this);
        }

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

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): self
    {
        // unset the owning side of the relation if necessary
        if ($assignment === null && $this->assignment !== null) {
            $this->assignment->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($assignment !== null && $assignment->getUser() !== $this) {
            $assignment->setUser($this);
        }

        $this->assignment = $assignment;

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
            $assignment->setUser($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): self
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getUser() === $this) {
                $assignment->setUser(null);
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
            $pointing->setUser($this);
        }

        return $this;
    }

    public function removePointing(Pointing $pointing): self
    {
        if ($this->pointings->removeElement($pointing)) {
            // set the owning side to null (unless already changed)
            if ($pointing->getUser() === $this) {
                $pointing->setUser(null);
            }
        }

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
            $foodPointing->setUser($this);
        }

        return $this;
    }

    public function removeFoodPointing(FoodPointing $foodPointing): self
    {
        if ($this->foodPointings->removeElement($foodPointing)) {
            // set the owning side to null (unless already changed)
            if ($foodPointing->getUser() === $this) {
                $foodPointing->setUser(null);
            }
        }

        return $this;
    }
}
