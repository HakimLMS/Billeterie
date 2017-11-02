<?php

namespace P4\BookingBundle\Controller;

use P4\BookingBundle\Entity\Books;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use P4\BookingBundle\Form\Type\BooksType;
use Stripe;
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
            
            //utilisation service checktype
            $checktype = $this->container->get('p4_booking.CheckPrice');
           
            
            
            $flash = $this->get('session')->getFlashBag();
            
            if ( $x === true )
            {
                
               $flash->add('fullbookeddate', 'Le date selectionnée pour votre reservation n\'est plus disponible.');
               return $this->redirectToRoute('p4_booking_books');
            }

                $checktype->amountType($book);
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
               
               $id = $book->getId();
               
               return $this->redirectToRoute('p4_booking_payements', array('id' => $id));          
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
    
    public function payAction(Request $request)
    {
        $id = $_GET['id'];
        
        $repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('P4BookingBundle:Books');
        
        $book = $repo->find($id);
        
        return $this->render('P4BookingBundle:Booking:stripepayements.html.twig', array('book' => $book));
    }
    
    public function chargeAction(Request $request)
    {
        // s'appuyer sur $book->getAmount().
        Stripe\Stripe::setApiKey("sk_test_W6WXT55oRpahbFejPnk2NWMl");
        
        $token = $_POST['stripeToken'];
        $id = $_POST['b-id'];
        
        $repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('P4BookingBundle:Books');
        
        $book = $repo->find($id);
        
        // Charge the user's card:
        $charge = Stripe\Charge::create(array(
        "amount" => $book->getAmount() * 100,
        "currency" => "eur",
        "description" => "Ticket Charge",
        "source" => $token,
        ));
      
      
    
    return $this->redirectToRoute('p4_booking_validation', array('id' => $id));
    }
    
    public function validationAction(Request $request)
    {
        $id = $_GET['id'];
        
        $repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('P4BookingBundle:Books');
        
        $book = $repo->find($id);
        
        $message = (new \Swift_Message('Validation'))
        ->setFrom('hlouahem@gmail.com')
        ->setTo($book->getMail())
        ->setBody(
            $this->renderView(
                'P4BookingBundle:Booking:Emails/mailer.html.twig',
                array('book' => $book)
            ),
            'text/html'
        );

        $mailer = $this->get('mailer');
        $mailer->send($message);
        var_dump($mailer);
        var_dump($message);

         return $this->redirectToRoute('p4_booking_homepage');
    }
    
}
