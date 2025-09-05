<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReceiptRepository;
use App\Service\ReceiptGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/receipt', name: 'api_receipt_')]
class ReceiptController extends AbstractController
{
    public function __construct(
        private ReceiptRepository $receiptRepository
    ) {
    }

    #[Route('/{id}', name: 'download', methods: 'GET')]
    public function download(User $syndic, ReceiptGenerator $generator): Response
    {
        $template = $this->receiptRepository->findOneBy(['createdBy' => $syndic]);

        // Build the template file path more reliably
        $templatePath = $this->getParameter('kernel.project_dir') . '/public/' . $template->getFilePath();

        // Debug: Check if file exists
        if (!file_exists($templatePath)) {
            return new JsonResponse([
                'error' => 'Template file not found',
                'path' => $templatePath,
                'project_dir' => $this->getParameter('kernel.project_dir'),
                'file_exists' => false
            ], 404);
        }

        // Check if file is readable
        if (!is_readable($templatePath)) {
            return new JsonResponse([
                'error' => 'Template file is not readable',
                'path' => $templatePath,
                'is_readable' => false
            ], 500);
        }

        try {
            // Dummy payment/user data
            $data = [
                'fullName' => $syndic->getFirstName() . ' ' . $syndic->getLastName(),
                'unitNumber' => $syndic->getUnits()->first()->getNumber(),
                'createdDate' => (new \DateTimeImmutable())->format('d/m/Y'),
                'amount' => '250 MAD',
            ];

            // Generate PDF
            $pdfBytes = $generator->generateToStream($template, $data);

            return new Response(
                $pdfBytes,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="receipt.pdf"',
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage(),
                'template_path' => $templatePath,
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
