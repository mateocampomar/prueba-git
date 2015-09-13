<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="compras canvas<?=($compra->status_compra) ? '' : ' anulado'?>" style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $compra->fechaManual )?></div>
	<h1>Compra: <strong><?=addZeros( $compra->id_compra )?></strong></h1>
	<div class="canvas-body">
		<?
		if (!$compra->status_compra)
		{
			?><div class="anulado">ANULADA</div><?
		}
		?>
		<table class="info">
			<tr>
				<td class="small" width="25%">Proveedor:</td>
				<td><?=$compra->nombre?></td>
			</tr>
			<tr>
				<td class="small">Facutura Porveedor:</td>
				<td><?=$compra->facturaNumero?></td>
			</tr>
			<tr>
				<td class="small">Depósito destino:</td>
				<td><?=$compra->deposito_nombre?></td>
			</tr>
			<?
			if ( $compra->detalle_compra )
			{
				?>
				<tr>
					<td class="small detalle">Detalle:</td>
					<td><p><?=nl2br( $compra->detalle_compra )?></p></td>
				</tr>
				<?
			}		
			?>
		</table>
		<div>
			<?=$crud->output?>
		</div>
		<br />
		<div class="footer_canvas">
			<table border="0" align="right">
				<tr>
					<td>Cantidad de Productos:</td>
					<td class="numb">x <?=$compra->cantidad ?></td>
				</tr>
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <?=currency_format( $compra->total )?></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <?=currency_format( $compra->iva  )?></td>
				</tr>
				<tr class="total">
					<td>Total:</td>
					<td class="numb">$ <?=currency_format( $compra->total_iva_inc )?></td>
				</tr>
			</table>
		</div>
		<hr/>
		<div class="datos">
			<?
			if ($compra->status_compra)
			{
				?>
				<div class="actions">
					<a href="<?=base_url('index.php/stock/deposito/' . $compra->id_compra )?>">Productos en Depósito de esta Compra</a> | <a href="javascript:anular()">Anular</a>
				</div>
				<?
			}
			?>
			Creada el: <?=$compra->fechaAuto?><br/>
		</div>
	</div>
</div>
<script type="text/javascript">
	function anular()
	{
		var r = confirm("Anular compra?");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/compras/anularCompra/' . $compra->id_compra)?>/";
		}
	}

</script>