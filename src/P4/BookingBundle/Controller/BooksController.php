<?php

namespace P4\BookingBundle\Controller;

use P4\BookingBundle\Entity\Books;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use P4\BookingBundle\Form\Type\BooksType;
use Symfony\Component\HttpFoundation\Session\Session;
use Swift_Image;
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

        return $this->render('P4BookingBundle:Booking:home.html.twig');
    }

  /**
     * Creates a new book entity.
     *
     * @Route("/book", name="books_new")
     * @Method({"POST"})
     */
    public function newAction(Request $request)
    {   
                
        $session = new Session();
        
        $book = new Books();
        $form = $this->get('form.factory')->create(BooksType::class,$book);

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
             
            $session->set('book', $book);
            
            return $this->redirectToRoute('p4_booking_payements');          
        }
        return $this->render('P4BookingBundle:Booking:booking.html.twig', array('book' => $book, 'form' => $form->createView()));
    }

    public function payAction(Request $request)
    {
        $session = new Session();
        $book = $session->get('book');
        
        return $this->render('P4BookingBundle:Booking:stripepayements.html.twig', array('book' => $book));
    }
    
    public function chargeAction(Request $request)
    {
        // s'appuyer sur $book->getAmount().
        Stripe\Stripe::setApiKey("sk_test_W6WXT55oRpahbFejPnk2NWMl");
        
        $token = $_POST['stripeToken'];
        $session = new Session();
        $book = $session->get('book');
         $flash = $this->get('session')->getFlashBag();
        try{
        $charge = Stripe\Charge::create(array(
        "amount" => $book->getAmount() * 100,
        "currency" => "eur",
        "description" => "Billeterie du Louvre",
        "source" => $token,
        ));
        }
         catch(\Stripe\Error\Card $e) {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err  = $body['error'];
       

        $flash->add('errorstripe', 'Votre carte a été déclinée, veuillez recommencer la saisie avec une carte valide.');
        return $this->redirectToRoute('p4_booking_payements');
      } 
        catch (\Stripe\Error\Base $e) {
           $flash->add('errorstripe', 'Une erreure est survenu lors de votre paiement, nous vous invitons à recommencer.');
           return $this->redirectToRoute('p4_booking_payements');
      } catch (Exception $e) {
            $flash->add('errorstripe', 'Une erreure est survenu lors de votre paiement, nous vous invitons à réitérer votre commande ultérieurement.');
           return $this->redirectToRoute('p4_booking_payements');
      }
      
      
    
    return $this->redirectToRoute('p4_booking_validation');
    }
    
    public function validationAction(Request $request)
    {
        $session = new Session();      
        $book = $session->get('book');
        
        $message = (new \Swift_Message('Validation'));
        $mail = $book->getMail();$image = 'http://hakimlouahem.com/public/img/louvre.png';
        $message
        ->setFrom(['hlouahem@gmail.com'=>'Billetterie du Louvre'])
        ->setTo($mail)
        ->setBody(
            $this->renderView(
                'P4BookingBundle:Booking:Emails/mailer.html.twig',
                array('book' => $book, 'image'=> $image)
            ),
            'text/html'
        );
        
        
        
        $mailer = $this->get('mailer');
        $mailer->send($message);
        if($book != null){
        //utilisation service SaveBook
        $savebook = $this->container->get('p4_booking.SaveBook');
        $savebook->saveAll($book);
        }
        
        
      
        $session->getFlashBag()->add('successBook', 'Votre commande à bien été enregistrée');
        
         return $this->redirectToRoute('p4_booking_homepage');
    }
    
}
