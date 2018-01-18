<?php

namespace LybraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LybraryBundle\Entity\Book;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use JMS\Serializer\SerializerBuilder;

class ApiController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->checkApiKey($request)) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'error' => 401,
                    'message' => $this->get('translator')->trans('invalid_api_key', array(), 'lybrary_trans')
                )
            );
        }

        $repository = $this->getDoctrine()->getManager();
        $books = $repository->getRepository('LybraryBundle:Book')->findBy(array(), array());

        $siteUrl = ($request->isSecure() ? 'https://' : 'http://') . $request->server->get('HTTP_HOST');

        foreach ($books as $book) {
            if ($book->getCover()) {
                $book->setCover($siteUrl . $this->getParameter('cover_directory_relative') . $book->getCover());
            }
            if ($book->getBookFile()) {
                $book->setBookFile($siteUrl . $this->getParameter('book_directory_relative').$book->getBookFile());
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
        if ($request->getMethod() == 'POST') {
            if (!$this->checkApiKey($request)) {
                return new JsonResponse(
                    array(
                        'success' => false,
                        'error' => 401,
                        'message' => $this->get('translator')->trans('invalid_api_key', array(), 'lybrary_trans')
                    )
                );
            }

            $requestData = $request->request->all();

            if (!empty($requestData['name']) &&
                !empty($requestData['author']) &&
                !empty($requestData['dateRead']) &&
                !empty($requestData['email'])
            ) {
                $repository = $this->getDoctrine()->getRepository('LybraryBundle:User');
                $user = $repository->findOneBy(array('email' => $requestData['email']));
                if (!$user) {
                    return new JsonResponse(
                        array(
                            'success' => false,
                            'error' => 402,
                            'message' => $this->get('translator')->trans('invalid_email', array(), 'lybrary_trans')
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
                $this->deleteCache();

                return new JsonResponse(
                    array(
                        'success' => true,
                        'error' => false,
                        'message' => $this->get('translator')->trans('add_book_ok', array(), 'lybrary_trans')
                    )
                );
            } else {
                return new JsonResponse(
                    array(
                        'success' => false,
                        'error' => 402,
                        'message' => $this->get('translator')->trans('invalid_parameters', array(), 'lybrary_trans')
                    )
                );
            }
        } else {
            return new JsonResponse(
                array(
                    'success' => false,
                    'error' => 405,
                    'message' => $this->get('translator')->trans('invalid_method', array(), 'lybrary_trans')
                )
            );
        }
    }

    public function editAction(Request $request, Book $book)
    {
        if ($request->getMethod() == 'POST') {
            if (!$this->checkApiKey($request)) {
                return new JsonResponse(
                    array(
                        'success' => false,
                        'error' => 401,
                        'message' => $this->get('translator')->trans('invalid_api_key', array(), 'lybrary_trans')
                    )
                );
            }

            $requestData = $request->request->all();

            if (!empty($requestData['name'])) {
                $book->setName($requestData['name']);
            }
            if (!empty($requestData['author'])) {
                $book->setAuthor($requestData['author']);
            }
            if (!empty($requestData['dateRead'])) {
                $book->setDateRead(new \DateTime($requestData['dateRead']));
            }
            $book->setAllowDownload($requestData['allowDownload']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            $this->deleteCache();

            return new JsonResponse(
                array(
                    'success' => true,
                    'error' => false,
                    'message' => $this->get('translator')->trans('edit_book', array(), 'lybrary_trans')
                )
            );
        } else {
            return new JsonResponse(
                array(
                    'success' => false,
                    'error' => 405,
                    'message' => $this->get('translator')->trans('invalid_method', array(), 'lybrary_trans')
                )
            );
        }
    }

    private function checkApiKey($request)
    {
        return $request->get("apiKey") == $this->getParameter('apiKey');
    }
    public function deleteCache()
    {
        $this->get('my_cache')->delete($this->getParameter('cache_books'));
    }
}
