<li id="<?=$insertId?>">
	<table width="100%" border="0">
		<tr>
			<td>
				<img src="<?=base_url('assets/img/web/catalogo/50x50/' . $producto->nombre_unico)?>.jpg" width="50" height="50" />
			</td>
			<td>
				<h6><?=$categoriaProducto->nombre?></h6>
				<p><?=$tipoProducto->nombre?> (<?=$producto->nombre_web?>)</p>
				<p>x <?=$nuevoRegistro->cantidad?></p>
			</td>
			<td valign="bottom" style="text-align:right; width:100px;">
				<p><?=currency_webFormat( $producto->precioWeb )?></p>
			</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:right;"><p><strong>TOTAL:</strong></p></td>
			<td style="text-align:right;"><p><strong><?=currency_webFormat( $producto->precioWeb * $nuevoRegistro->cantidad )?></strong></p></td>
		</tr>
	</table>
</li>