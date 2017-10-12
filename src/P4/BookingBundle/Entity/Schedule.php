<?php

namespace P4\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="P4\BookingBundle\Repository\ScheduleRepository")
 */
class Schedule
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
     * @var int
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;
    
    
    /**
     *
     * @var type date
     * 
     * @ORM\Column(name="date", type="datetime")
     * @ORM\ManyToOne(targetEntity="P4\BookingBundle\Tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $date;
    
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
     * Set count
     *
     * @param integer $count
     *
     * @return Schedule
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Schedule
     */
    public function setDate(\DateTime $date)
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
}
