<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<style type="text/css">
.bootstrap-timepicker-widget table{
  background-color: #fff !important;
}
</style>

<form action="<?=$_base['objeto']?>novo_grv" class="form-horizontal" method="post">             

  <fieldset>
    
    <div class="form-group">
      <label class="col-md-12">Dia da Semana</label>
      <div class="col-md-6">
        <select name="dia" class="form-control select2" style="width: 100%;" >
          <option value="0" selected="" >Domingo</option>
          <option value="1">Segunda</option>
          <option value="2">Terça</option>
          <option value="3">Quarta</option>
          <option value="4">Quinta</option>
          <option value="5">Sexta</option>
          <option value="6">Sábado</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-md-12" >Início</label>
      <div class="col-md-6">
        <div class="bootstrap-timepicker"> 
          <div class="input-group">
            <input type="text" class="form-control timepicker" name="inicio" value="<?=date('H:i')?>">
            <div class="input-group-addon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-md-12" >Nome do Programa</label>
      <div class="col-md-12">
        <input name="programa" type="text" class="form-control" >
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-md-12" >Apresentador</label>
      <div class="col-md-12">
        <input name="apresentador" type="text" class="form-control" >
      </div>
    </div>

    <div class="form-group">
      <label class="col-md-12" >Descrição</label>
      <div class="col-md-12">
        <textarea name="descricao" type="text" class="form-control" style="height:120px;" ></textarea>
      </div>
    </div>

    <div style="text-align: left; width: 100%;">
      <button type="submit" class="btn btn-primary">Salvar</button>
    </div>

  </fieldset>

</form>

<script type="text/javascript">
  $(document).ready(function() {

    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false
    });

  });
</script>