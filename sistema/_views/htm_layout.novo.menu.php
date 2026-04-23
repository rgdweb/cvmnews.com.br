<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<form action="<?=$_base['objeto']?>novo_menu_grv" class="form-horizontal" method="post">
	
	<fieldset>
		
		<div class="form-group">
			<label class="col-md-12" >Nome</label>
			<div class="col-md-12">
				<input name="nome" type="text" class="form-control" >
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-md-12" >Destino</label>
			<div class="col-md-12">
				<input name="destino" type="text" class="form-control" >
			</div>
		</div>
		
	</fieldset>
	
	<button type="submit" class="btn btn-primary">Salvar</button>
	
</form>
</section>