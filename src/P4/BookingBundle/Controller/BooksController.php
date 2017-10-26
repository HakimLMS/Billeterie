<?php

namespace P4\BookingBundle\Controller;

use P4\BookingBundle\Entity\Books;
use P4\BookingBundle\Entity\Price;
use P4\BookingBundle\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use P4\BookingBundle\Form\Type\BooksType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Book controller.
 *
 * @Route("books")
 */
class BooksController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="books_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('P4BookingBundle:Books')->findAll();

        return $this->render('P4BookingBundle:Booking:index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/book", name="books_new")
     * @Method({"POST"})
     */
    public function newAction(Request $request)
    {        
        $book = new Books();
        $form = $this->get('form.factory')->create(BooksType::class,$book);
        $em = $this->getDoctrine()->getManager();
        
        //price fixing
        
        // form handle
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            //utilisation service checkschedule
            $checkschedule = $this->container->get('p4_booking.CheckSchedule');
            $x = $checkschedule->isFree($book);
            
            
            $flash = $this->get('session')->getFlashBag();
            
            if ( $checkschedule->isFree($book) )
            {
               $flash->add('fullbookeddate', 'Le date selectionnée pour votre reservation n\'est plus disponible.');
               
            }
            else 
            {
                $this->amountType($book);
                
                var_dump($book->getAmount());
                die();
                $formticket = clone $book->getTicket();    
                $book->getTicket()->clear();

                // 1/2 flush book
                $em->persist($book);
                $em->flush();

                // 2/2 flush ticket
                foreach( $formticket as $t)
                {    
                    $t->setBooks($book);
                    $t->setDate($book->getDate());
                    $book->addTicket($t);
                    $em->persist($t);
                }
                
               $em->flush();
               $flash->add('successfullbookdate', 'Votre commande a bien été enregistrée');
               return $this->redirectToRoute('p4_booking_homepage');
            }           
        }
        return $this->render('P4BookingBundle:Booking:booking.html.twig', array('book' => $book, 'form' => $form->createView()));
    }

    /**
     * Finds and displays a book entity.
     *
     * @Route("/{id}", name="books_show")
     * @Method("GET")
     */
    public function showAction(Books $book)
    {
        $deleteForm = $this->createDeleteForm($book);

        return $this->render('books/show.html.twig', array(
            'book' => $book,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/{id}/edit", name="books_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Books $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm('P4\BookingBundle\Form\BooksType', $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('books_edit', array('id' => $book->getId()));
        }

        return $this->render('books/edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/{id}", name="books_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Books $book)
    {
        $form = $this->createDeleteForm($book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('books_index');
    }

    /**
     * Creates a form to delete a book entity.
     *
     * @param Books $book The book entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Books $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('books_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function payAction()
    {
        
        
        return $this->render('P4BookingBundle:Booking:stripepayements.html.twig');
    }
    
    public function chargeAction()
    {
        \Stripe\Stripe::setApiKey("sk_test_W6WXT55oRpahbFejPnk2NWMl");
        
        $token = $_POST['stripeToken'];
        
            // Charge the user's card:
    $charge = \Stripe\Charge::create(array(
      "amount" => 1000,
      "currency" => "eur",
      "description" => "Example charge",
      "source" => $token,
    ));
    }
    
    //set books amount.
    private function amountType(Books $book)
    {
        
        //Set variables
       $tickets= $book->getTicket();
       $price = new Price();       
       $today = new \DateTime('today');
       $em = $this->getDoctrine()->getManager();
       
       $totalAmount= 0;
       
       if($tickets != null)
       {
           
           //définit le lien entre type du ticekt et le type de tarrificiation. 
          foreach($tickets as $ticket)
          {
              
              $repo = $em->getRepository('P4BookingBundle:Price');
              
              //fixe l'interval de temps qui définit le prix du billet
              $birthdate = $ticket->getBirthDate();
              $Interval = $today->diff($birthdate);
              $Interval = $Interval->format('%Y');
              
              //créé la variable amount de la commande. (entité book)
              $amount = $book->getAmount();
       
              if($Interval <4)
              {
                $type ="4";
                $ticketprice = $repo->findPrice($type);
                $book->setAmount($amount + $ticketprice);    
              }
              elseif($ticket->getDiscount() == true)
              {   
                  $type = 'true';
                  $ticketprice = $repo->findPrice($type);
                  $book->setAmount($amount + $ticketprice);
                 
              }
              else
              {
                  switch(true){
                      case $Interval<12:
                          $type ="12";
                          $ticketprice = $repo->findPrice($type);
                          $book->setAmount($amount + $ticketprice);
                          break;
                      
                      case $Interval<60:
                          $type ="60";
                          $ticketprice = $repo->findPrice($type);
                          $book->setAmount($amount + $ticketprice);
                          break;
                      
                      case $Interval>=60:
                          $type ="+60";
                          $ticketprice = $repo->findPrice($type);
                          $book->setAmount($amount + $ticketprice);
                          break;
                  }
              }
  
          }
        }
    }
    
}
