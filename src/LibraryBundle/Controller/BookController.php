<?php

namespace LibraryBundle\Controller;

use LibraryBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Filesystem;

class BookController extends Controller
{
    public function indexAction(Request $request)
    {
        /*$fs = new Filesystem();

        $str = '2018/01/15/ec0c2b6f66c871e05a89f9a14782a9ca.jpeg';
        $rest = substr($str, 0, 12);
dump($rest);
dump($this->getParameter('cover_directory_relative'));
dump($this->getParameter('kernel.project_dir'));
dump($fs->exists($rest));
        $siteUrl =
            $this->getParameter('kernel.project_dir') .
            '/web' .
            $this->getParameter('cover_directory_relative') .
            $rest;
        dump($siteUrl);
        dump($fs->exists($siteUrl));*/

        $limit = $request->query->getInt('limit', $this->getParameter('count_book'));
        if ($limit > 100 || $limit <= 0) {
            $limit = $this->getParameter('count_book');
        }

        $cacheTTL = $this->getParameter('cache_ttl');
        $page = $request->query->getInt('page', 1);

        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->getBooks($page, $limit, $cacheTTL, $this->get('knp_paginator'));

        /*if (!$books->getItems()) {
            throw $this->createNotFoundException($this->get('translator')->trans('error.404.title'));
        }*/
        return $this->render('LibraryBundle:book:index.html.twig', array('books' => $books));
    }

    public function newAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm('LibraryBundle\Form\BookType', $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('LibraryBundle:book:new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
            'error' => '',
        ));
    }

    public function showAction(Book $book)
    {
        return $this->render('LibraryBundle:book:show.html.twig', array('book' => $book));
    }

    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm('LibraryBundle\Form\BookType', $book);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('book_edit', array('id' => $book->getId()));
        }

        return $this->render(
            'LibraryBundle:book:edit.html.twig',
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
