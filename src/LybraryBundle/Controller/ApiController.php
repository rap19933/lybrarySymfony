<?php

namespace LybraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LybraryBundle\Entity\Book;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


class ApiController
{

    public function indexAction(Request $request)
    {
        echo('<pre>');
        print_r($request);
        echo('</pre>');
       /* $repository = $this->getDoctrine()->getManager();
        $books = $repository->getRepository('LybraryBundle:Book')->findBy(array(), array("dateRead" => "DESC"));

        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
        $jsonContent = $serializer->serialize($books, 'json');*/
       // dump($jsonContent);
        echo('-------------------------------------------------------------------');
//a;
        //return new Response($jsonContent);
        return new Response('5555555555555555555');
    }
    public function addAction(Request $request)
    {
        a;
    }
    public function editAction(Request $request, Book $book)
    {
        echo('<pre>');
        //print_r($request);
        echo('</pre>');
        echo('<pre>');
        print_r($book);
        echo('</pre>');
        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
        $jsonContent = $serializer->serialize($book, 'json');
        return new Response($jsonContent);
    }
}
