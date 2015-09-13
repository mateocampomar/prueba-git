<script type="text/javascript">

	function renumerar()
	{
		
		var reciboNumero=prompt("Recibo Número:");

		if (reciboNumero!=null)
		{
			window.location.href = "<?=base_url('index.php/recibo/cambiarNumeroRecibo/' . $recibo->id)?>/" + reciboNumero;
		}
	}
	
	function anular()
	{
		var r = confirm("Anular el recibo?");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/recibo/anularRecibo/' . $recibo->id)?>/";
		}
	}

</script>
<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="ventas canvas<?=($recibo->status) ? '' : ' anulado'?> " style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $recibo->fechaManual )?></div>
	<h1>Recibo: <strong><?= addZeros( $recibo->reciboNumero )?></strong></h1>
	<?
	if ( $recibo->status == 1 )
		{
			?>
			<div class="message-box">
				<div class="flash_alert">Recibo No cobrado. <a data-href="<?=base_url('index.php/recibo/cobrarRecibo/' . $recibo->id)?>" class="btn" data-toggle="confirmation" data-placement="bottom" data-original-title="">Marcar como cobrado?</a></div>
			</div>
			<?
		}
	?>
	<div class="canvas-body">
		<?
		if (!$recibo->status)
		{
			?><div class="anulado">ANULADO</div><?
		}
		?>
		<table class="info">
			<tr>
				<td class="small" width="30%" style="vertical-align:top;">Local:</td>
				<td>
					<?=nl2br($recibo->localNombre)?>
					<div style="font-size:10pt; line-height: 2em; color: #666; padding-bottom:20px;">
						<?
						echo ( !empty( $reciboLocal->razon_social ) )	? $reciboLocal->razon_social . "<br/>"	: "";
						echo ( !empty( $reciboLocal->rut ) )			? $reciboLocal->rut . "<br/>"			: "";
						echo ( !empty( $reciboLocal->direccion ) )		? $reciboLocal->direccion . "<br/>"		: "";
						?>
					</div>
				</td>
			</tr>
			<?
			
			if ( $recibo->status == 2 )
			{
				?>
				<td class="small">Fecha de Cobro:</td>
				<td><p><?=nl2br($recibo->fechaCobro)?></p></td>
				<?
			}
			
			if ( $recibo->detalle )
			{
				?>
				<td class="small detalle">Detalle:</td>
				<td><p><?=nl2br($recibo->detalle)?></p></td>
				<?
			}		
			?>
		</table>
		<div>
			<?
			if (count($productosDelRecibo))
			{
				echo $crud->output;
			}
			?>
		</div>
		<br/>
		<div class="footer_canvas">
			<table border="0" align="right">
				<?
				if (count($productosDelRecibo))
				{
					?>
					<tr>
						<td>Cantidad de Productos:</td>
						<td class="numb">x <?=$totalCantidadProductos?></td>
					</tr>
					<?
				}
				?>
				<tr>
					<td>Subtotal:</td>
					<td width="100" class="numb">$ <?=currency_format( $recibo->total / $this->config->item('iva') )?></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <?=currency_format( $recibo->total - $recibo->total / $this->config->item('iva') )?></td>
				</tr>
				<tr class="total">
					<td>TOTAL:</td>
					<td class="numb">$ <?=currency_format($recibo->total)?></td>
				</tr>
			</table>
		</div>
		<hr/>
		<div class="datos">
			<?
			if ($recibo->status)
			{
				?>
				<div class="actions">
					<a href="javascript:anular()">Anular</a> | <a href="javascript:renumerar()">Renumerar</a>
				</div>
				<?
			}
			?>
			Creado el: <?=$recibo->fechaAuto?><br/>
		</div>
	</div>
</div>