<?php

namespace App\Service;

use setasign\Fpdi\Fpdi;
use App\Entity\Receipt;

class ReceiptGenerator
{
    public function generateToStream(Receipt $template, array $data): string
    {
        $filePath = $template->getFilePath();

        // Validate file path
        if (empty($filePath)) {
            throw new \InvalidArgumentException('File path is empty');
        }

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Template file does not exist: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException("Template file is not readable: {$filePath}");
        }

        try {
            $pdf = new Fpdi();
            $pdf->setSourceFile($filePath);
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx);
            $pdf->SetFont('Helvetica', '', 12);

            foreach ($template->getPlaceholders() as $ph) {
                $value = $data[$ph['key']] ?? '';
                $pdf->SetFontSize($ph['fontSize']);
                $pdf->SetXY($ph['x'], $ph['y']);
                $pdf->Write(8, $value);
            }

            return $pdf->Output('S');
        } catch (\Exception $e) {
            throw new \RuntimeException("PDF generation failed: " . $e->getMessage(), 0, $e);
        }
    }
}
