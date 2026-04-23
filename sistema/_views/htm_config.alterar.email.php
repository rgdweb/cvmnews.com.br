<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<form action="<?=$_base['objeto']?>alterar_email_grv" class="form-horizontal" method="post" >  						
  
  <fieldset>
    
    <div class="form-group">
      <label class="col-md-12" >Nome</label>
      <div class="col-md-12">
        <input name="titulo" type="text" class="form-control" value="<?=$data->titulo?>" >
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-md-12" >E-mail</label>
      <div class="col-md-12">
        <input name="email" type="text" class="form-control" value="<?=$data->email?>" >
      </div>
    </div>
    
  </fieldset>
  
  <div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <input type="hidden" name="id" value="<?=$data->id?>">
  </div>
  
</form>