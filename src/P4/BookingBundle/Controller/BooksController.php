<?php

namespace P4\BookingBundle\Controller;

use P4\BookingBundle\Entity\Books;
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

        return $this->render('books/index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/book", name="books_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        
        $book = new Books();
        $form = $this->get('form.factory')->create(BooksType::class,$book);
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            //utilisation service checkschedule
            $checkschedule = $this->container->get('p4_booking.CheckSchedule');
            $checked = $checkschedule->isFree($book);
            
            $flash = $this->get('session')->getFlashBag();
                    
            if ($checked['true'] != true || $checked != true )
            {
               $flash->add('fullbookeddate', 'Le date que vous selectionné pour votre reservation n\'est plus disponible. Il reste seulement '.$checked['available'].'places disponibles');
               $flash->get('fullbookeddate');
               
            }
            else
            {
                $flash->add('succesfullbookdate', 'Votre commande a bien été enregistrée');
                $flash->get('succesfullbookdate');
                
            }
            
            
        $formticket = clone $book->getTicket();    
        $book->getTicket()->clear();
        
        
        $em->persist($book);
        $em->flush();
        
        
        foreach( $formticket as $t)
            {    
                $t->setBooks($book);
                $book->addTicket($t);
                $em->persist($t);
            }
        $em->flush();    
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
}
