<?php

namespace P4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use P4\BookingBundle\Entity\Books;
use P4\BookingBundle\Form\BooksType;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller
{
    public function indexAction()
    {
        return $this->render('P4BookingBundle:Booking:index.html.twig');
    }
    
    public function bookAction(Request $request)
    {
        $book = new Books();
        $form = $this->get('form.factory')->create(BooksType::class,$book);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
        $book->getTicket()->clear();    
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();
        
        foreach( $form->get('ticket')->getData() as $t)
        {    
            $t->setBook($book);
            $em->persist($t);
        }
        
        $em->flush();
        }
        
        return $this->render('P4BookingBundle:Booking:booking.html.twig', array('book' => $book, 'form' => $form->createView()));
    }
}
