<?php

namespace P4\BookingBundle\CheckSchedule;

class CheckSchedule
{
    public function isFree($date, $count)
    {
        $repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('P4BookingBundle:Schedule');
        
        $scheduled = $repo->findBy($date);
        if( $scheduled->getSchedule() + $count > 1000  )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}