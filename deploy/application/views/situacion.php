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

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }

  function drawChart2() {
    var data = google.visualization.arrayToDataTable([
		<?=$chartHeader?>,
		<?=$chartText2?>
    ]);

    var options = {
		is3D: true,
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

    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart.draw(data, options);
  }
</script>
<div class="charts canvas" style="width: 1000px">
	<h1>
		<?
		if (isset( $localObj->nombre ))
		{
			?>
			Situación: <strong><?=$localObj->nombre?></strong> <span><a href="<?=base_url('index.php/stock/resumen_local')?>">Resumen</a> | <a href="<?=base_url('index.php/cuenta')?>">Estado de Cuenta</a> | <a href="<?=base_url('index.php/local/situacion')?>"><b>Situación</b></a></span>
			<?
		}
		else
		{
			?>
			<strong>Situación</strong> <span><a href="<?=base_url('index.php/stock')?>">Listado</a> | <a href="<?=base_url('index.php/charts/stockPorProductoTipo')?>">Gráfico: Por Colección</a> | <a href="<?=base_url('index.php/local/situacion')?>"><b>Situación</b></a></span>
			<?
		}
		?>
	</h1>
	<table border="0" width="100%">
		<tr>
			<td align="center" width="50%"><div id="piechart" style="width: 450px; height: 450px;"></div></td>
			<td align="center" width="50%"><div id="piechart2" style="width: 450px; height: 450px;"></div></td>
		</tr>
	</table>
</div>