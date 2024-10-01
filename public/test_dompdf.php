<?php

require '../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar DOMPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$html = '<h1>Factura de Compra</h1><p>Este es un PDF de prueba generado sin Symfony.</p>';
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="factura_test.pdf"');
echo $dompdf->output();
exit;
