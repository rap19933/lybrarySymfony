<?php

namespace LybraryBundle\Controller;

use LybraryBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    public function indexAction(Request $request)
    {
        $cache = $this->get('cache_books_service');
        $cacheId = $this->getParameter('cache_books_id');

        if (!$cache->contains($cacheId)) {
            $books = $this->getDoctrine()->getRepository(Book::class)->getBooks();
            $cache->save($cacheId, $books, $this->getParameter('cache_ttl'));
        } else {
            $books = $cache->fetch($cacheId);
        }

        return $this->render(
            'LybraryBundle:book:index.html.twig',
            array(
                'books' => $books,
                'cover_directory_relative' => $this->getParameter('cover_directory_relative'),
                'book_directory_relative' => $this->getParameter('book_directory_relative')
            )
        );
    }

    public function newAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm('LybraryBundle\Form\BookType', $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('LybraryBundle:book:new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
            'error' => '',
        ));
    }

    public function showAction(Book $book)
    {
        return $this->render(
            'LybraryBundle:book:show.html.twig',
            array(
                'cover_directory_relative' => $this->getParameter('cover_directory_relative'),
                'book_directory_relative' => $this->getParameter('book_directory_relative'),
                'book' => $book
            )
        );
    }

    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm('LybraryBundle\Form\BookType', $book);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('book_edit', array('id' => $book->getId()));
        }

        return $this->render(
            'LybraryBundle:book:edit.html.twig',
            array(
                'book' => $book,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    public function deleteAction(Book $book)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('book_index');
    }

    private function createDeleteForm(Book $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('book_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
