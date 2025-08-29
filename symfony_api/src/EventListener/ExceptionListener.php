<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    public function __construct(
        private SerializerInterface $serializer,
        private string $environment,
        private ?LoggerInterface $logger = null
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        // Log the exception for debugging
        if ($this->logger) {
            $this->logger->error('API Exception: ' . $exception->getMessage(), [
                'exception' => $exception,
                'request_uri' => $request->getRequestUri(),
                'request_method' => $request->getMethod(),
            ]);
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

            default => $this->createInternalServerErrorResponse($exception)
        };

        $event->setResponse($response);
    }

    private function createInternalServerErrorResponse(\Throwable $exception): JsonResponse
    {
        $data = [
            'error' => 'Internal server error'
        ];

        // In development mode, include detailed error information
        if ($this->environment === 'dev') {
            $data['debug'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'type' => get_class($exception)
            ];

            // If it's a Doctrine exception, include more specific details
            if ($exception instanceof \Doctrine\DBAL\Exception) {
                $data['debug']['sql_error'] = true;
                $data['debug']['doctrine_message'] = $exception->getMessage();
            }
        }

        return new JsonResponse($data, 500);
    }
}
