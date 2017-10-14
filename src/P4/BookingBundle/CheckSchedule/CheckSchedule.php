<?php

use Doctrine\ORM\EntityManager;

namespace P4\BookingBundle\CheckSchedule;

class CheckSchedule
{
    private $repo;
    
    public function __construct(\P4\BookingBundle\Repository\ScheduleRepository $repo)
    {
        $this->repo = $repo;
    }
    public function isFree(\P4\BookingBundle\Entity\Books $book)
    {
        $date = $book->getDate();
        $count = count($book->getTicket());
        
        $repo= $this->repo;
        
        $scheduled = $repo->findDate($date);
        var_dump($scheduled);
        
        if( count($scheduled) + $count > 1000 || $scheduled = null )
        {
            
            return true;
        }
        else
        {
            $repo->update($scheduled, $count, $date);
            return false;
        }
    }
}