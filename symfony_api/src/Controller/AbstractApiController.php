<?php

namespace App\Controller;

use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator,
        protected ?LoggerInterface $logger = null
    ) {
    }

    protected function jsonResponse($data, int $status = 200, array $groups = []): JsonResponse
    {
        try {
            // Method 1: Try using JsonResponse directly (it handles serialization)
            if (empty($groups)) {
                return new JsonResponse($data, $status);
            }

            // Method 2: Use Symfony serializer with groups
            $context = ['groups' => $groups];

            // First, let's validate that serialization works
            $json = $this->serializer->serialize($data, 'json', $context);

            // Validate that we got valid JSON
            if (json_decode($json) === null && json_last_error() !== JSON_ERROR_NONE) {
                if ($this->logger) {
                    $this->logger->error("Serialization produced invalid JSON", [
                        'json_error' => json_last_error_msg(),
                        'data_type' => get_class($data),
                        'partial_json' => substr($json, 0, 200)
                    ]);
                }
                throw new \Exception("Serialization failed: " . json_last_error_msg());
            }

            // Return with pre-encoded JSON
            return new JsonResponse($json, $status, [], true);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error("jsonResponse method failed", [
                    'error' => $e->getMessage(),
                    'data_type' => is_object($data) ? get_class($data) : gettype($data),
                    'groups' => $groups
                ]);
            }

            // Fallback: try simple JSON response without serializer
            try {
                return new JsonResponse([
                    'error' => 'Serialization failed',
                    'message' => $e->getMessage(),
                    'fallback_data' => $this->convertToArray($data)
                ], 500);
            } catch (\Exception $fallbackE) {
                return new JsonResponse([
                    'error' => 'Complete serialization failure',
                    'primary_error' => $e->getMessage(),
                    'fallback_error' => $fallbackE->getMessage()
                ], 500);
            }
        }
    }

    private function convertToArray($data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            // Use reflection to convert object to array
            $reflection = new \ReflectionClass($data);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

            $result = [];
            foreach ($properties as $property) {
                $result[$property->getName()] = $property->getValue($data);
            }
            return $result;
        }

        return ['value' => $data];
    }

    protected function validateRequest(object $dto): void
    {
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            throw new ValidationException($errors);
        }
    }
}
