<script type="text/javascript">

	function anular()
	{
		var r = confirm("Anular el Nota de Crédito?");
		if (r == true)
		{
			window.location.href = "<?=base_url('index.php/nc/anularNc/' . $nc->id)?>/";
		}
	}

</script>
<style>
	.pDiv2 {
		display: none;
	}
</style>
<div class="nc canvas<?=($nc->status) ? '' : ' anulado'?> " style="width:660px;">
	<div class="fecha_right"><?=fechaToText( $nc->fechaManual )?></div>
	<h1>Nota de Crédito: <strong><?=addZeros( $nc->ncNumero )?></strong></h1>
	<div class="canvas-body">
		<?
		if (!$nc->status)
		{
			?><div class="anulado">ANULADA</div><?
		}
		?>
		<table class="info">
			<tr>
				<td class="small" width="30%" style="vertical-align:top;">Local:</td>
				<td>
					<?=nl2br($nc->localNombre)?>
					<div style="font-size:10pt; line-height: 2em; color: #666; padding-bottom:20px;">
						<?
						echo ( !empty( $ncLocal->razon_social ) )	? $ncLocal->razon_social . "<br/>"	: "";
						echo ( !empty( $ncLocal->rut ) )			? $ncLocal->rut . "<br/>"			: "";
						echo ( !empty( $ncLocal->direccion ) )		? $ncLocal->direccion . "<br/>"		: "";
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td class="small">Depósito de Destino:</td>
				<td><?=$nc->deposito_nombre?></td>
			</tr>
			<?
			if ( $nc->detalle )
			{
				?>
				<tr>
					<td class="small detalle">Detalle:</td>
					<td><p><?=nl2br($nc->detalle)?></p></td>
				</tr>
				<?
			}		
			?>
		</table>
		<br/>
		<?
		if ( count($productosDeLaNc) )
		{
			echo $crud->output;
		}
		?>
		<br />
		<div class="footer_canvas">
			<table border="0" align="right">
				<?
				if ( count($productosDeLaNc) )
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
					<td width="100" class="numb">$ <?=currency_format( $nc->total / $this->config->item('iva') )?></td>
				</tr>
				<tr>
					<td>IVA:</td>
					<td class="numb">$ <?=currency_format( $nc->total - $nc->total / $this->config->item('iva') )?></td>
				</tr>
				<tr class="total">
					<td>TOTAL:</td>
					<td class="numb">$ <?=currency_format($nc->total)?></td>
				</tr>
			</table>
		</div>
		<hr/>
		<div class="datos">
			<?
			if ( $nc->status )
			{
				?>
				<div class="actions">
					<a href="javascript:anular()">Anular</a>
				</div>
				<?
			}
			?>
			Creado el: <?=$nc->fechaAuto?><br/>
		</div>
	</div>
</div>