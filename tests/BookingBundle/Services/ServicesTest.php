<?php
 namespace P4\BookingBundle\Tests\Services;
 
 use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
 use P4\BookingBundle\Entity\Tickets;
 use P4\BookingBundle\Entity\Books;
 use DateTime;
 
 class ServiceTest extends WebTestCase
 {

     public function testPrice()
     {
          
          $b = new Books();
          $t = new Tickets();
         
          $date = New DateTime('10/06/1989');
          $t->setBirthDate($date);
          $b->addTicket($t);          
          
          $kernel = static::createKernel();
          $kernel->boot();
          
          $container = $kernel->getContainer(); 
          $service = $container->get('p4_booking.CheckPrice');
          $service->amountType($b);
          
          $this->assertEquals(16, $b->getAmount());
          
          
     }
     
     public function testSchedule()
     {
         
         $date = New DateTime('11/11/2017');
         $b = new Books();
         $b->setDate($date);
         
         $kernel = static::createKernel();
         $kernel->boot();
         $container = $kernel->getContainer();
         $service = $container->get('p4_booking.CheckSchedule');
         $free = $service->isFree($b);
         
         
         //even if 11/11 is booked the book contain no tickets so return false
         $this->assertEquals(false, $free);
         
         
         
     }
     
 }

