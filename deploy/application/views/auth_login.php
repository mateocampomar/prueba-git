<br/>
<br/>
<br/>
<br/>
<div style="text-align:center;">
	<img src="<?=base_url('assets/img/logo-home.png')?>" style="width:300px;height:300px;"/>
</div>
<br/>
<br/>
<br/>
<br/>
<div class="canvas" style="width:600px; margin-top:0px;">
	<h1>Login</h1>
	<div class="canvas-body">
		<div class="form_errors">
			<?php echo validation_errors(); ?>
		</div>
		<?=form_open(base_url( 'index.php/auth/login' ))?>
		<table class="info">
			<tr>
				<td align="center"><?=form_input($email)?></td>
				<td align="center"><?=form_input($password)?></td>
			</tr>
		</table>
		<hr/>
		<div class="submit" style="width:90%;">
			<?=form_submit('mysubmit', 'Login')?>
		</div>
		<?=form_close()?>
	</div>
</div>