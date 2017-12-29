<?php

namespace LybraryBundle\Controller;

use LybraryBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LybraryBundle\ClassForFiles;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Book controller.
 *
 */
class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     */
    public function indexAction(Request $request)
    {
        if (preg_match('#^[0-9]+$#', $request->query->get("countShow"))){
            $countShow = $request->query->get("countShow");
        } else {
            $countShow =  null;
        }

        if($request->query->get("myShow") == 1){
            $filter = array("user" => $this->getUser()->getId());
            $myShow = 1;
        } else {
            $filter = array();
            $myShow = 0;
        }

        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('LybraryBundle:Book')->findBy($filter,array("dateRead" => "DESC"), $countShow);

        return $this->render('book/index.html.twig', array(
            'books' => $books,
            'countShow' => $countShow,
            'myShow' => $myShow,
            'cover_directory_relative' => $this->getParameter('cover_directory_relative'),
            "book_directory_relative" => $this->getParameter('book_directory_relative'),
        ));
    }

    /**
     * Creates a new book entity.
     *
     */
    public function newAction(Request $request)
    {
        if(!$this->getUser())
        {
            return $this->render('FOSUserBundle:Security:login.html.twig',
                array('last_username' => '',
                      'error' => '',
                      'csrf_token' => '')
            );
        }

        $book = new Book();
        $form = $this->createForm('LybraryBundle\Form\BookType', $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$book->getBookFile()) {
                return $this->render('book/new.html.twig', array(
                    'book' => $book,
                    'form' => $form->createView(),
                    'error' => 'error'
                ));
            }

            $book->setUser($this->getUser());

            $book->setDateRead(date_create(date("Y-m-d H:i:s")));

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
            'error' => ''
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     */
    public function showAction(Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);

        return $this->render('book/show.html.twig', array(
            'book' => $book,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     */
    public function editAction(Request $request, Book $book)
    {

        if(!$this->getUser() || $this->getUser()->getId() !== $book->getUser()->getId())
        {
            return $this->render('FOSUserBundle:Security:login.html.twig',
                array('last_username' => '',
                      'error' => '',
                      'csrf_token' => '')
            );
        }

        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm('LybraryBundle\Form\BookType', $book);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_edit', array('id' => $book->getId()));
        }

        return $this->render('book/edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     */
    public function deleteAction(Request $request, Book $book)
    {
        $form = $this->createDeleteForm($book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();
        }
        return $this->redirectToRoute('book_index');
    }

    /**
     * Creates a form to delete a book entity.
     *
     * @param Book $book The book entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Book $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('book_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
