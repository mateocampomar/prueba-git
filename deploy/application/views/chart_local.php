<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Genre', 'Ventas', 'Consignación', { role: 'annotation' } ],
			<?
			foreach($locales as $local)
			{
				echo "['" . $local['nombre'] . " (" . round( $local['situacion'], 2 ) . ")', " . (($local['venta']->total) ? $local['venta']->total : 0) . ", " . (($local['consignacion']->total) ? $local['consignacion']->total : 0) . ", ''],"."\n";
			}
			?>
		]);
	
		var options = {
			width: 1000,
			height: 600,
			legend: { position: 'top', maxLines: 3, textStyle: { fontSize: 13 }, },
			bar: { groupWidth: '75%' },
			isStacked: true,
			fontSize: 13,
		};

		var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		chart.draw(data, options);
	}



</script>
<div class="charts canvas" style="width: 1000px">
	<h1><strong>Locales (Ventas vs. Consignación)</strong> <span><a href="<?=base_url('index.php/stock/local')?>">Resumen</a> | <a href="<?=base_url('index.php/charts/local')?>"><b>Gráfico: Ventas vs Consignación</b></a></span></h1>
	<div id="chart_div">	
	</div>
</div>