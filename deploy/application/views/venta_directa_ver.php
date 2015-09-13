<script type="text/javascript">
	function anularFactura()
	{
		var r = confirm("Anular la venta directa?");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/venta_directa/anular/' . $venta->id)?>";
		}
	}

</script>
<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="venta_directa canvas<?=($venta->status) ? '' : ' anulado'?> " style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $venta->fechaManual )?></div>
	<h1>Venta directa: <strong><?=$venta->facturaNumero?></strong></h1>
	<div class="canvas-body">
		<?
		if (!$venta->status)
		{
			?><div class="anulado">ANULADA</div><?
		}
		?>
		<table class="info">
			<tr>
				<td class="small">Cliente:</td>
				<td><?=nl2br($venta->cliente)?></td>
			</tr>
			<?
			if ( $venta->detalle )
			{
				?>
				<tr>
					<td class="small detalle">Detalle:</td>
					<td><p><?=nl2br($venta->detalle)?></p></td>
				</tr>
				<?
			}		
			?>
		</table>
		<div>
			<?=$crud->output?>
		</div>
		<div class="footer_canvas">
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <?=$venta->cantidad ?></td>
				</tr>
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <?=currency_format( $venta->total )?></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <?=currency_format( $venta->iva  )?></td>
				</tr>
				<tr class="total">
					<td>TOTAL:</td>
					<td class="numb">$ <?=currency_format($venta->total_iva_inc)?></td>
				</tr>
			</table>
		</div>
		<hr/>
		<div class="datos">
			<?
			if ($venta->status)
			{
				?>
				<div class="actions">
					<a href="javascript:anularFactura()">Anular</a>
				</div>
				<?
			}
			?>
			Creado el: <?=$venta->fechaAuto?><br/>
		</div>
	</div>
</div>