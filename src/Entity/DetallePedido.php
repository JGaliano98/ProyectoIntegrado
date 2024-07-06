<?php

namespace App\Entity;

use App\Repository\DetallePedidoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetallePedidoRepository::class)]
class DetallePedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $precio = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre_producto = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion_producto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $precio_producto = null;

    #[ORM\ManyToOne(inversedBy: 'detallePedidos')]
    private ?Producto $producto = null;

    #[ORM\ManyToOne(inversedBy: 'detallePedidos')]
    private ?Pedido $pedido = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getNombreProducto(): ?string
    {
        return $this->nombre_producto;
    }

    public function setNombreProducto(string $nombre_producto): static
    {
        $this->nombre_producto = $nombre_producto;

        return $this;
    }

    public function getDescripcionProducto(): ?string
    {
        return $this->descripcion_producto;
    }

    public function setDescripcionProducto(string $descripcion_producto): static
    {
        $this->descripcion_producto = $descripcion_producto;

        return $this;
    }

    public function getPrecioProducto(): ?string
    {
        return $this->precio_producto;
    }

    public function setPrecioProducto(string $precio_producto): static
    {
        $this->precio_producto = $precio_producto;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): static
    {
        $this->producto = $producto;

        return $this;
    }

    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    public function setPedido(?Pedido $pedido): static
    {
        $this->pedido = $pedido;

        return $this;
    }
}
