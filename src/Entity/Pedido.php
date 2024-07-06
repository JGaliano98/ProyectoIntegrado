<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $total = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_pago = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre_usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $email_usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $calle_direccion = null;

    #[ORM\Column]
    private ?int $numero_direccion = null;

    #[ORM\Column(length: 255)]
    private ?string $localidad_direccion = null;

    #[ORM\Column(length: 255)]
    private ?string $provincia_direccion = null;

    #[ORM\Column]
    private ?int $codigo_postal_direccion = null;

    #[ORM\Column(length: 255)]
    private ?string $pais_direccion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $otra_info_direccion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaPago(): ?\DateTimeInterface
    {
        return $this->fecha_pago;
    }

    public function setFechaPago(\DateTimeInterface $fecha_pago): static
    {
        $this->fecha_pago = $fecha_pago;

        return $this;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombre_usuario;
    }

    public function setNombreUsuario(string $nombre_usuario): static
    {
        $this->nombre_usuario = $nombre_usuario;

        return $this;
    }

    public function getEmailUsuario(): ?string
    {
        return $this->email_usuario;
    }

    public function setEmailUsuario(string $email_usuario): static
    {
        $this->email_usuario = $email_usuario;

        return $this;
    }

    public function getCalleDireccion(): ?string
    {
        return $this->calle_direccion;
    }

    public function setCalleDireccion(string $calle_direccion): static
    {
        $this->calle_direccion = $calle_direccion;

        return $this;
    }

    public function getNumeroDireccion(): ?int
    {
        return $this->numero_direccion;
    }

    public function setNumeroDireccion(int $numero_direccion): static
    {
        $this->numero_direccion = $numero_direccion;

        return $this;
    }

    public function getLocalidadDireccion(): ?string
    {
        return $this->localidad_direccion;
    }

    public function setLocalidadDireccion(string $localidad_direccion): static
    {
        $this->localidad_direccion = $localidad_direccion;

        return $this;
    }

    public function getProvinciaDireccion(): ?string
    {
        return $this->provincia_direccion;
    }

    public function setProvinciaDireccion(string $provincia_direccion): static
    {
        $this->provincia_direccion = $provincia_direccion;

        return $this;
    }

    public function getCodigoPostalDireccion(): ?int
    {
        return $this->codigo_postal_direccion;
    }

    public function setCodigoPostalDireccion(int $codigo_postal_direccion): static
    {
        $this->codigo_postal_direccion = $codigo_postal_direccion;

        return $this;
    }

    public function getPaisDireccion(): ?string
    {
        return $this->pais_direccion;
    }

    public function setPaisDireccion(string $pais_direccion): static
    {
        $this->pais_direccion = $pais_direccion;

        return $this;
    }

    public function getOtraInfoDireccion(): ?string
    {
        return $this->otra_info_direccion;
    }

    public function setOtraInfoDireccion(?string $otra_info_direccion): static
    {
        $this->otra_info_direccion = $otra_info_direccion;

        return $this;
    }
}
