<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
 
  google.setOnLoadCallback(drawChart);
  google.setOnLoadCallback(drawChart2);
  google.setOnLoadCallback(drawChart3);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
		<?=$chartHeader?>,
		<?=$chartText?>
    ]);

    var options = {
		is3D: true,
		slices:	[{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},],
        legend: {
        			textStyle: {
        						fontSize: 13
							   },
					position: 'right',
				},
		title: 'En Unidades',
		legend: 'none',
        pieSliceText: 'label',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }
 
  function drawChart2() {
    var data = google.visualization.arrayToDataTable([
		<?=$chartHeader?>,
		<?=$chartTextTotal?>
    ]);

    var options = {
		is3D: true,
		slices:	[{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},],
        legend: {
        			textStyle: {
        						fontSize: 13
							   },
					position: 'right',
				},
		title: 'Facturacion ($u)',
		legend: 'none',
        pieSliceText: 'label',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart.draw(data, options);
  }

  function drawChart3() {
    var data = google.visualization.arrayToDataTable([
		<?=$chartHeader?>,
		<?=$chartTextGanancia?>
    ]);

    var options = {
		is3D: true,
		slices:	[{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},],
        legend: {
        			textStyle: {
        						fontSize: 13
							   },
					position: 'right',
				},
		title: 'Margen de Ganancia ($u)',
		legend: 'none',
        pieSliceText: 'label',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart3'));
    chart.draw(data, options);
  }
 
</script>
<div class="charts canvas" style="width: 1300px;">
	<h1><strong><?= $h1 ?></strong> <span><a href="<?=base_url('index.php/charts/ventasPorProducto')?>">Por Producto</a> | <a href="<?=base_url('index.php/charts/ventasPorProductoTipo')?>"><b>Gráfico: Por Colección</b></a> | <a href="<?=base_url('index.php/charts/ventasPorAno')?>">Acumulado Por Año</a> | <a href="<?=base_url('index.php/stock/ventasPorCanal')?>">Por Canal</a></span></h1>
	<table border="0" width="100%">
		<tr>
			<td align="center" width="33%"><div id="piechart" style="width: 400px; height: 400px;"></div></td>
			<td align="center" width="34%"><div id="piechart2" style="width: 400px; height: 400px;"></div></td>
			<td align="center" width="33%"><div id="piechart3" style="width: 400px; height: 400px;"></div></td>
		</tr>
	</table>
</div>