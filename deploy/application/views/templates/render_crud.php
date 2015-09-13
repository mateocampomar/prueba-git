<?
	if (isset($width100) && $width100)
	{
		$style = "";
	}
	else
	{
		$style = "width:638px;";
	}

?>
<style>

	#filtering_form {
		display: none;
	}
	


</style>
<div style="<?=$style?>">
	<?=$crud->output?>
</div>