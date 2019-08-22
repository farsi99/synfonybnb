<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Faker\Provider\DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $Ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Veuillez saisir une date au bon format")
     * @Assert\GreaterThan("today", message="La date d'arrivé doit être ultérieur à la date du jour !", groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Veuillez saisir une date au bon format")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de fin doit être supérieu à celle du debut !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /** 
     * Callbak qu'on appel à chaque enregistrement
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        //calcule du prix total
        if (empty($this->amount)) {
            //prix de l'annonce * nombre de jre
            $this->amount = $this->Ad->getPrice() * $this->getDuration();
        }
    }
    /**
     * Permet de recuperer un tableau des jours qui correspond à ma reservation
     *
     * @return array Un tableau d'objet dateTime
     */
    public function getDays()
    {
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );
        $days = array_map(function ($daysTimestamp) {
            return new \DateTime(date('Y-m-d', $daysTimestamp));
        }, $result);
        return $days;
    }
    //calcule la durée
    public function getDuration()
    {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    //
    public function isBookableDates()
    {
        //1 )il faut connaitre les dates impossible à reserver
        $notAvalableDays = $this->Ad->getNotAvalableDays();
        //2) il faut comparer les dates choisies avec les dates impossible
        $bookingDays = $this->getDays();

        $formtDays = function ($day) {
            return $day->format('Y-m-d');
        };
        //On boucle pour avoir un tableau qui contient des chaines des caracteres de mes journées disponible
        $days = array_map($formtDays, $bookingDays);

        //on boucle pour avoir les jours non disponible
        $notAvalable = array_map($formtDays, $notAvalableDays);

        foreach ($days as $day) {
            if (array_search($day, $notAvalable) !== false) return false;
        }
        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->Booker;
    }

    public function setBooker(?User $Booker): self
    {
        $this->Booker = $Booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->Ad;
    }

    public function setAd(?Ad $Ad): self
    {
        $this->Ad = $Ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
