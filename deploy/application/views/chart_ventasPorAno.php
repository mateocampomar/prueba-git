<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			<?=$chartHeader?>,
			<?=$chartText?>
		]);
	
		var options = {
			'chartArea':	{'width': '90%', 'height': '80%'},
			'vAxis':		{'format': '0'},
			'hAxis':		{
							'textStyle':		{
												'color':	'#999999',
												'fontSize':	'10'
												},
							'slantedTextAngle': 60,
							},
		};
	
		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

		chart.draw(data, options);
	}
</script>

<div class="charts canvas" style="width: 1000px">
	<h1><strong>Ventas por Año</strong> <span><a href="<?=base_url('index.php/charts/ventasPorProducto')?>">Por Producto</a> | <a href="<?=base_url('index.php/charts/ventasPorProductoTipo')?>">Gráfico: Por Colección</a> | <a href="<?=base_url('index.php/charts/ventasPorAno')?>"><b>Acumulado Por Año</b></a> | <a href="<?=base_url('index.php/stock/ventasPorCanal')?>">Por Canal</a></span></h1>
	<div id="chart_div" style="width: 1000px; height: 500px;"></div>
</div>