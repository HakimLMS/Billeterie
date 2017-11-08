<?php
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
        var_dump($date);
        $count = count($book->getTicket());
        $oldcount;
        $repo= $this->repo;
        
        $scheduled = $repo->findDate($date);
        var_dump($scheduled);

        
        if ($scheduled)
        {
            $oldcount = $scheduled->getCount();   
        }
        else
        {
            $oldcount = 0;
        }
        
        
        if( $oldcount + $count > 1000)
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