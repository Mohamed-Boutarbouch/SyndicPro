<?php

namespace App\Controller;

use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator
    ) {
    }

    protected function jsonResponse($data, int $status = 200, array $groups = []): JsonResponse
    {
        $context = [];
        if (!empty($groups)) {
            $context['groups'] = $groups;
        }

        $json = $this->serializer->serialize($data, 'json', $context);
        return new JsonResponse($json, $status, [], true);
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
