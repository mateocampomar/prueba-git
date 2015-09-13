<?

$pdf->AddPage();
$pdf->SetFont('Arial','', 10);

function calcularY( $copia, $y )
{
	return $copia * 150 + $y;
}

// Dos copias en la misma hoja
for($copia=0; $copia < 2; $copia++ )
{
	// Fecha de la Factura
	$pdf->SetY( calcularY($copia, 15) );
	$pdf->SetX( 140 );
	$pdf->Cell( 50,	5, fechaToText( $oficina->fechaManual ),	0,	0, 'R' );



	// Datos de Facturación
	// Razón Social
	//$pdf->SetY( calcularY($copia, 15) );
	$pdf->SetX( 15 );
	$pdf->Cell( 100,	5, utf8_decode( $oficina->cliente ),		0,	1, 'C' );
	
	// RUT
	//$pdf->SetY( calcularY($copia, 25) );
	$pdf->SetX( 15 );
	$pdf->Cell( 100,	5, 'RUT: ' . utf8_decode( $oficina->rut ),	0,	1, 'C' );
	
	// Direccion
	//$pdf->SetY( calcularY($copia, 35) );
	$pdf->SetX( 15 );
	$pdf->Cell( 100,	5, utf8_decode( $oficina->direccion ),		0,	1, 'C' );
	
	
	
	// Productos
	$pdf->SetY( calcularY($copia, 50) );
	foreach ( $oficinaProductos as $producto )
	{
		$pdf->SetX( 25 );
		$pdf->Cell( 20,		5, $producto->cantidad,														0,	0,	'L');
		$pdf->Cell( 100,	5, utf8_decode( $producto->nombre ),										0,	0,	'L' );
		$pdf->Cell( 20,		5, currency_format( $producto->producto_precio * $producto->cantidad ),		0,	1,	'R' );
	}
	
	
	
	// Totales
	$pdf->SetY( calcularY($copia, 100) );
	$pdf->SetX( 145 );
	$pdf->Cell( 20,	5, currency_format( $oficina->total ),				0,	1,	'R' );
	
	$pdf->SetX( 145 );
	$pdf->Cell( 20,	5, currency_format( $oficina->iva ),				0,	1,	'R' );
	
	$pdf->SetX( 145 );
	$pdf->Cell( 20,	5, currency_format( $oficina->total_iva_inc ),		0,	1,	'R' );
}



$pdf->Output();