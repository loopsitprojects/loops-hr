<?php

$fileName = __DIR__ . '/dummy_cv.pdf';

// Content to draw
$content = "BT\n/F1 24 Tf\n50 700 Td\n(WARDIERE UNIVERSITY) Tj\n0 -50 Td\n(Fauget Studio) Tj\n0 -50 Td\n(Isabel Schumacher) Tj\n0 -30 Td\n(Graphics Designer) Tj\n0 -50 Td\n(hello@reallygreatsite.com) Tj\n0 -30 Td\n(+123-456-7890) Tj\nET";
$streamLen = strlen($content);

// Construct PDF
$pdf = "%PDF-1.4\n";
$pdf .= "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n";
$pdf .= "2 0 obj <</Type /Pages /Kids [3 0 R] /Count 1>> endobj\n";
$pdf .= "3 0 obj <</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
$pdf .= "4 0 obj <</Length $streamLen>> stream\n$content\nendstream\nendobj\n";
$pdf .= "5 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>> endobj\n";

$xrefOffset = strlen($pdf);
$objects = [
    1 => strpos($pdf, "1 0 obj"),
    2 => strpos($pdf, "2 0 obj"),
    3 => strpos($pdf, "3 0 obj"),
    4 => strpos($pdf, "4 0 obj"),
    5 => strpos($pdf, "5 0 obj"),
];

$pdf .= "xref\n";
$pdf .= "0 6\n";
$pdf .= "0000000000 65535 f \n";
foreach ($objects as $offset) {
    $pdf .= sprintf("%010d 00000 n \n", $offset);
}
$pdf .= "trailer <</Size 6 /Root 1 0 R>>\n";
$pdf .= "startxref\n$xrefOffset\n%%EOF";

file_put_contents($fileName, $pdf);
echo "PDF created at $fileName\n";
