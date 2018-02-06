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

        $siteUrl = $request->getSchemeAndHttpHost();

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
        $serializer = SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
        $jsonContent = $serializer->serialize($books, 'json');

        return new Response($jsonContent);
    }

    public function addAction(Request $request)
    {
        $requestData = $request->request->all();
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
