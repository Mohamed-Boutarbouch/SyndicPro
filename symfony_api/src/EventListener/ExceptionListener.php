<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $response = match (true) {
            $exception instanceof ValidationException => new JsonResponse([
                'error' => 'Validation failed',
                'violations' => $exception->getErrors()
            ], 400),

            $exception instanceof NotFoundHttpException => new JsonResponse([
                'error' => 'Resource not found'
            ], 404),

            $exception instanceof AccessDeniedHttpException => new JsonResponse([
                'error' => 'Access denied'
            ], 403),

            default => new JsonResponse([
                'error' => 'Internal server error'
            ], 500)
        };

        $event->setResponse($response);
    }
}
