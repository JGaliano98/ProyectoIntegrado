<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido1 = null;

    #[ORM\Column(length: 255)]
    private ?string $apellido2 = null;

    #[ORM\Column]
    private ?int $telefono = null;

    /**
     * @var Collection<int, Resena>
     */
    #[ORM\OneToMany(targetEntity: Resena::class, mappedBy: 'user')]
    private Collection $resenas;

    /**
     * @var Collection<int, MetodoPago>
     */
    #[ORM\OneToMany(targetEntity: MetodoPago::class, mappedBy: 'user')]
    private Collection $metodoPagos;

    /**
     * @var Collection<int, Pedido>
     */
    #[ORM\OneToMany(targetEntity: Pedido::class, mappedBy: 'user')]
    private Collection $pedidos;

    /**
     * @var Collection<int, Direccion>
     */
    #[ORM\OneToMany(targetEntity: Direccion::class, mappedBy: 'user')]
    private Collection $direccions;

    public function __construct()
    {
        $this->resenas = new ArrayCollection();
        $this->metodoPagos = new ArrayCollection();
        $this->pedidos = new ArrayCollection();
        $this->direccions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido1(): ?string
    {
        return $this->apellido1;
    }

    public function setApellido1(string $apellido1): static
    {
        $this->apellido1 = $apellido1;

        return $this;
    }

    public function getApellido2(): ?string
    {
        return $this->apellido2;
    }

    public function setApellido2(string $apellido2): static
    {
        $this->apellido2 = $apellido2;

        return $this;
    }

    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(int $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * @return Collection<int, Resena>
     */
    public function getResenas(): Collection
    {
        return $this->resenas;
    }

    public function addResena(Resena $resena): static
    {
        if (!$this->resenas->contains($resena)) {
            $this->resenas->add($resena);
            $resena->setUser($this);
        }

        return $this;
    }

    public function removeResena(Resena $resena): static
    {
        if ($this->resenas->removeElement($resena)) {
            // set the owning side to null (unless already changed)
            if ($resena->getUser() === $this) {
                $resena->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetodoPago>
     */
    public function getMetodoPagos(): Collection
    {
        return $this->metodoPagos;
    }

    public function addMetodoPago(MetodoPago $metodoPago): static
    {
        if (!$this->metodoPagos->contains($metodoPago)) {
            $this->metodoPagos->add($metodoPago);
            $metodoPago->setUser($this);
        }

        return $this;
    }

    public function removeMetodoPago(MetodoPago $metodoPago): static
    {
        if ($this->metodoPagos->removeElement($metodoPago)) {
            // set the owning side to null (unless already changed)
            if ($metodoPago->getUser() === $this) {
                $metodoPago->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pedido>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedido $pedido): static
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos->add($pedido);
            $pedido->setUser($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): static
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getUser() === $this) {
                $pedido->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Direccion>
     */
    public function getDireccions(): Collection
    {
        return $this->direccions;
    }

    public function addDireccion(Direccion $direccion): static
    {
        if (!$this->direccions->contains($direccion)) {
            $this->direccions->add($direccion);
            $direccion->setUser($this);
        }

        return $this;
    }

    public function removeDireccion(Direccion $direccion): static
    {
        if ($this->direccions->removeElement($direccion)) {
            // set the owning side to null (unless already changed)
            if ($direccion->getUser() === $this) {
                $direccion->setUser(null);
            }
        }

        return $this;
    }
}
