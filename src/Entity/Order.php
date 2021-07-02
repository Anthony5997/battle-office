<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payment_method;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity=Client::class, cascade={"persist", "remove"})
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $id_api_response;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, cascade={"persist", "remove"})
     */
    private $order_product;

    /**
     * @ORM\OneToOne(targetEntity=AddressBilling::class, cascade={"persist", "remove"})
     */
    private $address_billing;



    public function __construct()
    {
        $this->product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): self
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getIdApiResponse(): ?string
    {
        return $this->id_api_response;
    }

    public function setIdApiResponse(string $id_api_response): self
    {
        $this->id_api_response = $id_api_response;

        return $this;
    }

    public function getOrderProduct(): ?Product
    {
        return $this->order_product;
    }

    public function setOrderProduct(?Product $order_product): self
    {
        $this->order_product = $order_product;

        return $this;
    }

    public function getAddressBilling(): ?AddressBilling
    {
        return $this->address_billing;
    }

    public function setAddressBilling(?AddressBilling $address_billing): self
    {
        $this->address_billing = $address_billing;

        return $this;
    }
}
