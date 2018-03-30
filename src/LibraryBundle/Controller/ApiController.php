<?php

namespace LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LibraryBundle\Entity\Book;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ApiController extends Controller
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
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

        $siteUrl = $request->getSchemeAndHttpHost();
        $books = $books->getItems();
        foreach ($books as $book) {
            if ($book->getCover()) {
                $book->setCover($siteUrl . $this->getParameter('cover_directory_relative') . $book->getCover());
            }
            if ($book->getBookFile()) {
                $book->setBookFile(
                    $siteUrl . $this->getParameter('book_directory_relative') . $book->getBookFile()
                );
            }
        }

        $jsonContent = $this->serializer->serialize($books, 'json');

        return new JsonResponse(
            array(
                'success' => true,
                'error' => false,
                'message' => $jsonContent
            )
        );
    }

    public function addAction(Request $request)
    {
        $requestData = $request->request->all();
        /*unset ($requestData['apiKey']);
        unset ($requestData['email']);*/


        /*$requestBook = array(
            'name' => $requestData['name'],
            'author' => $requestData['author'],
            'dateRead' => $requestData['dateRead'],
            'allowDownload' => $requestData['allowDownload'],
            );*/
        //$book = new Book();
        /*$jsonContent = $this->serializer->serialize($requestData, 'json');
        return new JsonResponse(
            array(
                'success' => true,
                'error' => false,
                'message' => $requestData
            )
        );*/
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');

        return new JsonResponse(
            array(
                'success' => false,
                'error' => false,
                'message' => $book
            )
        );
        $this->trimData($requestData);

        $validator = Validation::createValidator();
        $violations = $validator->validate(
            $requestData['email'],
            array(new Assert\Email(), new Assert\NotBlank())
        );
        if ($violation = $this->checkValidator($violations)) {
            return new JsonResponse(array('success' => false, 'error' => 402, 'message' => $violation));
        }

        $violations = $validator->validate(
            $requestData['dateRead'],
            array(new Assert\Date(), new Assert\NotBlank())
        );
        if ($violation = $this->checkValidator($violations)) {
            return new JsonResponse(array('success' => false, 'error' => 402, 'message' => $violation));
        }

        if (!empty($requestData['name']) && !empty($requestData['author'])) {
            $repository = $this->getDoctrine()->getRepository('LibraryBundle:User');
            $user = $repository->findOneBy(array('email' => $requestData['email']));
            if (!$user) {
                return new JsonResponse(
                    array(
                        'success' => false,
                        'error' => 402,
                        'message' => $this->get('translator')->trans('invalid_email')
                    )
                );
            }

            $book = new Book();
            $book->setName($requestData['name']);
            $book->setAuthor($requestData['author']);
            $book->setDateRead(new \DateTime($requestData['dateRead']));
            $book->setAllowDownload($requestData['allowDownload']);
            $book->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return new JsonResponse(
                array(
                    'success' => true,
                    'error' => false,
                    'message' => $this->get('translator')->trans('add_book_ok')
                )
            );
        } else {
            return new JsonResponse(
                array(
                    'success' => false,
                    'error' => 402,
                    'message' => $this->get('translator')->trans('invalid_parameters')
                )
            );
        }
    }

    public function editAction(Request $request, Book $book)
    {
        $requestData = $request->request->all();
        $this->trimData($requestData);

        if (!empty($requestData['name'])) {
            $book->setName($requestData['name']);
        }
        if (!empty($requestData['author'])) {
            $book->setAuthor($requestData['author']);
        }
        if (!empty($requestData['dateRead'])) {
            $violations = Validation::createValidator()->validate($requestData['dateRead'], array(new Assert\Date()));
            if ($violation = $this->checkValidator($violations)) {
                return new JsonResponse(
                    array(
                        'success' => false,
                        'error' => 402,
                        'message' => $violation
                    )
                );
            }
            $book->setDateRead(new \DateTime($requestData['dateRead']));
        }
        $book->setAllowDownload($requestData['allowDownload']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return new JsonResponse(
            array(
                'success' => true,
                'error' => false,
                'message' => $this->get('translator')->trans('edit_book')
            )
        );
    }

    private function trimData(&$data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = trim($value);
        }
    }

    private function checkValidator($violations)
    {
        if (count($violations) !== 0) {
            foreach ($violations as $violation) {
                return $violation->getMessage();
            }
        }
    }
}
