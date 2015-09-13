<br/>
<h2><u>Materia Prima de la Producción:</u></h2>
<br/>
<table border="0" width="100%" cellspacing="1" class="prodSelect">
	<tr class="title">
		<td>Nombre</td>
		<td align="right">Precio Unitario</td>
		<td align="right">Cantidad Necesaira</td>
		<td align="right">En Stock</td>
	</tr>
	<?
	$cssClass = "trdark";
	
	foreach($productosArray as $key => $producto)
	{
		$cssClass = ($cssClass == 'trlight') ? 'trdark' : 'trlight';
	
		?>
		<tr class="<?=$cssClass?>">
			<td height="40"><?=$producto['nombre']?></td>
			<td align="right">$ <?=currency_format( $producto['precio'] )?></td>
			<td width="30" align="right">x <?=$producto['cantidadOrigen']?></td>
			<td width="30" align="right">x <?=$producto['enstock']?></td>
		</tr>
		<?
	}
	?>
</table>