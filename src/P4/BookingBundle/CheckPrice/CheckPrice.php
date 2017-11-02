<?php

namespace P4\BookingBundle\CheckPrice;

class CheckPrice 
{
    private $repo;
    
    public function __construct(\P4\BookingBundle\Repository\PriceRepository $repo) {
        $this->repo = $repo;
    }
    
    
    //set books amount.
    public function amountType(\P4\BookingBundle\Entity\Books $book)
    {
        
        //Set variables
       $tickets= $book->getTicket();       
       $today = new \DateTime('today');
       
       $totalAmount= 0;
       
       if($tickets != null)
       {
           
           //définit le lien entre type du ticekt et le type de tarrificiation. 
          foreach($tickets as $ticket)
          {
              
              $repo = $this->repo;
              
              //fixe l'interval de temps qui définit le prix du billet
              $birthdate = $ticket->getBirthDate();
              $Interval = $today->diff($birthdate);
              $Interval = $Interval->format('%Y');
              
              //créé la variable amount de la commande. (entité book)
              $amount = $book->getAmount();
       
              if($Interval <4)
              {
                $type ="4";  
              }
              elseif($ticket->getDiscount() == true)
              {   
                  $type = 'true';                 
              }
              else
              {
                  switch(true){
                      case $Interval<12:
                          $type ="12";                                              
                          break;
                      
                      case $Interval<60:
                          $type ="60";
                          break;
                      
                      case $Interval>=60:
                          $type ="+60";
                          break;
                  }
              }                
              $ticketprice = $repo->findPrice($type);              
              $book->setAmount($amount + $ticketprice);
          }
        }
    }
    
}

