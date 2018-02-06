<?php

namespace LibraryBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RequestSubscriber
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false !== strpos($request->attributes->get('_controller'), 'ApiController')) {
            if ($this->apiKey != $request->get('apiKey')) {
                $response = new JsonResponse(
                    array(
                        'success' => false,
                        'error'   => Response::HTTP_UNAUTHORIZED,
                        'message' => 'Invalid Api Key'
                    )
                );
                $event->setResponse($response);
            }
        }
    }
}
