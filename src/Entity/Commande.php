<?php
// src/Entity/Commande.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[Groups(["Commande:read,Plat:read"])]

#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plat::class)]
    #[ORM\JoinColumn(name:'id_plat', nullable: false)]
    private ?Plat $id_plat = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name:'id_client', nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'integer', options: ["default" => 0])]
    private int $etat = 0;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlat(): ?Plat
    {
        return $this->id_plat;
    }

    public function setClient(?Client $plat): self
    {
        $this->client = $plat;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setPlat(?Plat $plat): self
    {
        $this->id_plat = $plat;
        return $this;
    }
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }


    public function setEtat(int $etat): self
    {
        $this->etat = $etat;
        return $this;
    }
    const ETAT_EN_ATTENTE = 0;
    const ETAT_VALIDE = 1;
    const ETAT_LIVREE = 2;


    public function getEtat()
    {
        switch ($this->etat) {
            case self::ETAT_EN_ATTENTE:
                return 'En attente';
            case self::ETAT_VALIDE:
                return 'Validée';
            case self::ETAT_LIVREE:
                return 'Livrée';
            default:
                return 'Inconnu';
        }
    }
}
