<?php

namespace App\Service;

use setasign\Fpdi\Fpdi;
use App\Entity\ReceiptTemplate;

class ReceiptGenerator
{
    public function generateToStream(ReceiptTemplate $template, array $data): string
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

            // Import first page of template
            $tplIdx = $pdf->importPage(1);

            // Get template dimensions (orientation, width, height)
            $size = $pdf->getTemplateSize($tplIdx);

            // Create a page with the same orientation and size
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

            // Place template on the page
            $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

            // Default font
            $pdf->SetFont('Helvetica', '', 12);

            // Placeholders rendering with styling
            foreach ($template->getPlaceholders() as $ph) {
                $value = $data[$ph['key']] ?? '';

                // Set font (family, style, size)
                $pdf->SetFont(
                    $ph['fontFamily'] ?? 'Helvetica',
                    $ph['fontStyle'] ?? '',
                    $ph['fontSize'] ?? 12
                );

                // Set text color if defined
                if (isset($ph['color']) && is_array($ph['color'])) {
                    [$r, $g, $b] = $ph['color'];
                    $pdf->SetTextColor($r, $g, $b);
                } else {
                    $pdf->SetTextColor(0, 0, 0); // reset to black
                }

                // Set position
                $pdf->SetXY($ph['x'], $ph['y']);

                $lineHeight = $ph['lineHeight'] ?? 8;
                $align = $ph['align'] ?? 'L';

                if (isset($ph['background']) && is_array($ph['background'])) {
                    [$r, $g, $b] = $ph['background'];
                    $pdf->SetFillColor($r, $g, $b);

                    // Draw text inside a filled cell
                    $pdf->Cell(
                        $ph['width'] ?? 80, // default width
                        $lineHeight,
                        $value,
                        0,
                        0,
                        $align,
                        true // fill enabled
                    );
                } else {
                    // Normal write without background
                    $pdf->Write($lineHeight, $value);
                }
            }

            return $pdf->Output('S');
        } catch (\Exception $e) {
            throw new \RuntimeException("PDF generation failed: " . $e->getMessage(), 0, $e);
        }
    }
}
