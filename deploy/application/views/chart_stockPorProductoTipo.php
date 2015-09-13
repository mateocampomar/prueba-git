<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
 
  google.setOnLoadCallback(drawChart);
  google.setOnLoadCallback(drawChart2);

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
		title: 'En $',
		legend: 'none',
        pieSliceText: 'label',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart.draw(data, options);
  }
 
</script>
<div class="charts canvas" style="width: 85%">
	<h1><strong><?= $h1 ?></strong> <span><a href="<?=base_url('index.php/stock')?>">Listado</a> | <a href="<?=base_url('index.php/charts/stockPorProductoTipo')?>"><b>Gráfico: Por Colección</b></a> | <a href="<?=base_url('index.php/local/situacion')?>">Situación</a></span></h1>
	<table border="0" width="100%">
		<tr>
			<td align="center" width="50%"><div id="piechart" style="width: 550px; height: 550px;"></div></td>
			<td align="center" width="50%"><div id="piechart2" style="width: 550px; height: 550px;"></div></td>
		</tr>
	</table>
</div>