<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<form action="<?=$_base['objeto']?>alterar_resposta_grv" class="form-horizontal" method="post">
  
  <fieldset>
    
    <div class="form-group">
      <label class="col-md-12" >Resposta</label>
      <div class="col-md-12">
        <input name="resposta" type="text" class="form-control" value="<?=$data->resposta?>">
      </div>
    </div>

  </fieldset>

  <div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <button type="button" class="btn btn-default" onclick="confirma('<?=$_base['objeto']?>apagar_resposta/codigo_enquete/<?=$codigo_enquete?>/codigo/<?=$codigo?>');">Apagar</button>
    <input type="hidden" name="codigo" value="<?=$codigo?>">
    <input type="hidden" name="codigo_enquete" value="<?=$codigo_enquete?>">
  </div>
  
</form>