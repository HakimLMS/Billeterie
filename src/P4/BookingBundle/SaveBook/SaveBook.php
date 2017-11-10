<?php
namespace P4\BookingBundle\SaveBook;

use P4\BookingBundle\Entity\Books;

class SaveBook
{
    private $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em){
        
        $this->em = $em;
        
    }
    
    public function saveAll(Books $book)
    {
        $em = $this->em;
        
        $formticket = clone $book->getTicket();
               
                $book->getTicket()->clear();

                foreach( $formticket as $t)
                {    
                    $t->setBooks($book);
                    $t->setDate($book->getDate());
                    $book->addTicket($t);
                    $em->persist($t);
                    
                }
                $em->persist($book);
                $em->flush();
    }
    
}