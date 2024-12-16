<?php

namespace App\Entity;

use App\Repository\CompaniaTransporteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompaniaTransporteRepository::class)]
class CompaniaTransporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $costeEnvio = null;

    #[ORM\Column]
    private ?int $tiempoEntrega = null;

    /**
     * @var Collection<int, Pedido>
     */
    #[ORM\OneToMany(targetEntity: Pedido::class, mappedBy: 'companiaTransporte')]
    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCosteEnvio(): ?string
    {
        return $this->costeEnvio;
    }

    public function setCosteEnvio(string $costeEnvio): static
    {
        $this->costeEnvio = $costeEnvio;

        return $this;
    }

    public function getTiempoEntrega(): ?int
    {
        return $this->tiempoEntrega;
    }

    public function setTiempoEntrega(int $tiempoEntrega): static
    {
        $this->tiempoEntrega = $tiempoEntrega;

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
            $pedido->setCompaniaTransporte($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): static
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getCompaniaTransporte() === $this) {
                $pedido->setCompaniaTransporte(null);
            }
        }

        return $this;
    }
}
