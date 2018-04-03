<?php

namespace LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use LibraryBundle\Entity\Book;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use JMS\Serializer\SerializerBuilder;

class ApiController extends Controller
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->addDefaultListeners()
            ->build();
    }
    public function indexAction(Request $request)
    {
        $limit = $request->query->getInt('limit', $this->getParameter('count_book'));
        if ($limit > 100 ) {
            $limit = $this->getParameter('count_book');
        }

        $cacheTTL = $this->getParameter('cache_ttl');
        $page = $request->query->getInt('page', 1);

        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->getBooks($page, $limit, $cacheTTL, $this->get('knp_paginator'));

        $books = $books->getItems();

        $jsonContent = $this->get('jms_serializer')->serialize($books, 'json');

        return $this->response(true, false, $jsonContent);
    }

    public function addAction(Request $request)
    {
        if (filter_var($request->query->get('email'), FILTER_VALIDATE_EMAIL)) {
            $repository = $this->getDoctrine()->getRepository('LibraryBundle:User');
            $user = $repository->findOneBy(array('email' => $request->query->get('email')));
        }
        $error = [];
        if (!isset($user)) {
            $error[] = $this->get('translator')->trans('invalid_email');
        }

        try {
            $book = $this->get('jms_serializer')->deserialize($request->getContent(), Book::class, 'json');
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        if (empty($error)) {
            if (!count($this->get('validator')->validate($book))) {
                $book->setUser($user);
                $em = $this->getDoctrine()->getManager();
                $em->persist($book);
                $em->flush();
                return $this->response(true, false, $this->get('translator')->trans('add_book_ok'));
            } else {
                $error[] = $this->get('translator')->trans('invalid_parameters');
            }
        }

        return $this->response(false, 402, $error);
    }

    public function editAction(Request $request, Book $book)
    {

        try {
            $bookEdit = $this->get('jms_serializer')->deserialize($request->getContent(), Book::class, 'json');
        } catch (\Exception $e) {
            return $this->response(false, 402, $e->getMessage());
        }

        if (!empty($bookEdit->getName())) {
            $book->setName($bookEdit->getName());
        }
        if (!empty($bookEdit->getAuthor())) {
            $book->setAuthor($bookEdit->getAuthor());
        }
        if (!empty($bookEdit->getDateRead())) {
            $book->setDateRead($bookEdit->getDateRead());
        }
        if (!empty($bookEdit->getAllowDownload())) {
            $book->setAllowDownload(1);
        }  else {
            $book->setAllowDownload(0);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return $this->response(true, false, $this->get('translator')->trans('edit_book_ok'));
    }

    private function response($success, $error, $message)
    {
        return new JsonResponse(
            array(
                'success' => $success,
                'error' => $error,
                'message' => $message
            )
        );
    }
}
