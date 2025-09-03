<?php

namespace App\Controller;

use App\DTO\Request\RecordPaymentRequest;
use App\DTO\Response\ResidentsResponse;
use App\Enum\LedgerEntryPaymentMethod;
use App\Repository\BuildingRepository;
use App\Service\ContributionPaymentService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/buildings', name: 'api_buildings_')]
final class BuildingController extends AbstractController
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private ValidatorInterface $validator,
        private ContributionPaymentService $contributionPaymentService,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/{buildingId}/residents', methods: ['GET'], name: 'residents')]
    public function residents(int $buildingId): Response
    {
        $stats = $this->buildingRepository->getResidentsByBuilding($buildingId);

        return $this->json(
            ResidentsResponse::fromDataArray($stats),
            status: 200,
            context: ['groups' => ['form']]
        );
    }

    #[Route('/{buildingId}/payment/contribution', methods: ['POST'], name: 'create_payment_contribution')]
    public function createPayment(
        int $buildingId,
        Request $request
    ): JsonResponse {
        try {
            // Log the raw request data for debugging
            $this->logger->info('Payment request received', [
                'buildingId' => $buildingId,
                'content' => $request->getContent(),
                'headers' => $request->headers->all()
            ]);

            // Get the JSON content
            $jsonContent = $request->getContent();
            if (empty($jsonContent)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Empty request body'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Parse JSON manually first
            $data = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid JSON: ' . json_last_error_msg()
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Parsed JSON data', ['data' => $data]);

            // Create RecordPaymentRequest manually to avoid MapRequestPayload issues
            try {
                $paymentRequest = $this->createPaymentRequestFromData($data);
                $this->logger->info('Created payment request object', [
                    'unitId' => $paymentRequest->unitId,
                    'amount' => $paymentRequest->amount,
                    'paymentDate' => $paymentRequest->paymentDate?->format('Y-m-d H:i:s'),
                    'paymentMethod' => $paymentRequest->paymentMethod?->value
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Failed to create payment request object', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to process request data: ' . $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            $errors = $this->validator->validate($paymentRequest);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                $this->logger->warning('Validation failed', ['errors' => $errorMessages]);

                return $this->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Processing payment', ['buildingId' => $buildingId]);
            $paymentRecord = $this->contributionPaymentService->recordPayment($buildingId, $paymentRequest);

            $this->logger->info('Payment processed successfully', ['paymentId' => $paymentRecord->getId()]);

            return $this->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Invalid argument exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error processing payment', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'buildingId' => $buildingId
            ]);
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createPaymentRequestFromData(array $data): RecordPaymentRequest
    {
        $paymentDate = null;
        if (isset($data['paymentDate'])) {
            try {
                $paymentDate = new \DateTimeImmutable($data['paymentDate']);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid payment date format: ' . $e->getMessage());
            }
        }

        $paymentMethod = null;
        if (isset($data['paymentMethod'])) {
            try {
                $paymentMethod = LedgerEntryPaymentMethod::from($data['paymentMethod']);
            } catch (\ValueError $e) {
                throw new \InvalidArgumentException('Invalid payment method: ' . $data['paymentMethod']);
            }
        }

        return new RecordPaymentRequest(
            unitId: (int)($data['unitId'] ?? 0),
            amount: (float)($data['amount'] ?? 0.0),
            paymentDate: $paymentDate,
            paymentMethod: $paymentMethod,
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null
        );
    }
}
