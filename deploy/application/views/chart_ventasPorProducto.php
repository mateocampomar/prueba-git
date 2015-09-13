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
		is3D: true,
		slices:	[{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},{offset: 0.1},],
        legend: {
        			textStyle: {
        						fontSize: 13
							   },
					position: 'right',
				},
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }
</script>
<div class="charts canvas" style="width: 1000px">
	<h1><strong><?= $h1 ?></strong> <span><a href="<?=base_url('index.php/charts/ventasPorProducto')?>"><b>Por Producto</b></a> | <a href="<?=base_url('index.php/charts/ventasPorProductoTipo')?>">Gráfico: Por Colección</a> | <a href="<?=base_url('index.php/charts/ventasPorAno')?>">Acumulado Por Año</a> | <a href="<?=base_url('index.php/stock/ventasPorCanal')?>">Por Canal</a></span></h1>
	<div id="piechart" style="width: 1000px; height: 600px;"></div>
</div>