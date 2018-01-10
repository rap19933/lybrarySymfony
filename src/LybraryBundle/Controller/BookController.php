<?php

namespace LybraryBundle\Controller;

use LybraryBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Simple\FilesystemCache;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Book controller.
 *
 */
class BookController extends Controller
{
    private $cache;
    public function __construct()
    {
        $this->cache = new FilesystemCache();
    }
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

        if ($myShow || $countShow) {
            $repository = $this->getDoctrine()->getManager();
            $books = $repository->getRepository('LybraryBundle:Book')->findBy($filter,array("dateRead" => "DESC"), $countShow);
        } else {
            $serializer = \JMS\Serializer\SerializerBuilder::create()
                ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
                ->build();

            if (!$this->cache->has($this->getParameter('cache_books'))) {
                $repository = $this->getDoctrine()->getManager();
                $books = $repository->getRepository('LybraryBundle:Book')->findBy($filter,array("dateRead" => "DESC"), $countShow);

                $jsonContent = $serializer->serialize($books, 'xml');
                $this->cache->set($this->getParameter('cache_books'), $jsonContent, $this->getParameter('cache_ttl'));

            } else {

                $books = $serializer->deserialize($this->cache->get($this->getParameter('cache_books')), 'array<LybraryBundle\Entity\Book>', 'xml');
            }
        }

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
        $apiKey = md5($this->getParameter('apiKey'));

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

            if ($request->request->get("apiKey") != $apiKey) {
                return $this->render('book/error.html.twig');
            };

            if (!$book->getBookFile()) {
                return $this->render('book/new.html.twig', array(
                    'book' => $book,
                    'form' => $form->createView(),
                    'error' => 'error',
                    'apiKey' => $apiKey
                ));
            }

            $book->setUser($this->getUser());

            $book->setDateRead(date_create(date("Y-m-d H:i:s")));

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            $this->deleteCache();
            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
            'error' => '',
            'apiKey' => $apiKey
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     */
    public function showAction(Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);

        $book->setDateRead(date_create(date("Y-m-d H:i:s")));

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();
        $this->deleteCache();

        return $this->render('book/show.html.twig', array(
            'cover_directory_relative' => $this->getParameter('cover_directory_relative'),
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
            $this->deleteCache();
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
            $this->deleteCache();
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
    public function deleteCache()
    {
        $this->cache->delete($this->getParameter('cache_books'));
    }

    public function errorAction()
    {
        return $this->render('book/error.html.twig');
    }
}
