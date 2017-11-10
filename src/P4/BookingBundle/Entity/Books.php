<?php

namespace P4\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use P4\BookingBundle\Entity\Tickets;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Books
 *
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="P4\BookingBundle\Repository\BooksRepository")
 */
class Books
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
    
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="P4\BookingBundle\Entity\Tickets", mappedBy="books", cascade={"persist"})
     */
    private $tickets;
    
    /**
     * @var int
     * 
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     * 
     * @ORM\Column(name="serial", type="string", length=255)
     */
    private $serial;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Books
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return Books
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Books
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Books
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Books
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    /**
     * Add ticket
     *
     * @param \P4\BookingBundle\Tickets $ticket
     *
     * @return Books
     */
    public function addTicket(Tickets $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \P4\BookingBundle\Tickets $ticket
     */
    public function removeTicket(Tickets $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTicket()
    {
        return $this->tickets;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Books
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }
    
    /**
     * Set serial
     * 
     * @return Tickets
     */
    public function setSerial()
    {
        $n = $this->name;
        $s = $this->surname;
        $d = $this->date;
        
        $base = $n + $s + $d->format('dd/MM');
        $serial = sha1($base);
        
        $this->serial = $serial;
    }
    
    /**
     * Get serial
     * 
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }
            
    

}
