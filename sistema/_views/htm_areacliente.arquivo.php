<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<form action="<?=$_base['objeto']?>arquivo_grv" class="form-horizontal" method="post" enctype="multipart/form-data" >
  
  <fieldset>
				
				<div class="form-group">
					<label class="col-md-12" >Titulo</label>
					<div class="col-md-12">
						<input name="titulo" type="text" class="form-control" >
					</div>
				</div>
        
        <div class="form-group">
          <label class="col-md-12" >Arquivo</label>
          <div class="col-md-12">
            <div class="fileupload fileupload-new" data-provides="fileupload">
              <div class="input-append">
                <div class="uneditable-input">
                  <i class="fa fa-file fileupload-exists"></i>
                  <span class="fileupload-preview"></span>
                </div>
                <span class="btn btn-default btn-file">
                  <span class="fileupload-exists">Alterar</span>
                  <span class="fileupload-new">Procurar arquivo</span>
                  <input type="file" name="arquivo" />
                </span>
                <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remover</a>
              </div>
            </div>
          </div>
        </div>

        <div style="padding-top:10px; padding-bottom:20px;"><strong>Disponibilizar arquivo para:</strong></div>
        
        <div class="form-group">
          <div class="col-md-12">
          <?php
            
            foreach ($usuarios as $key => $value) {

              if($value['codigo'] == $codigo){
                $check = " checked='' ";
              } else {
                $check = "";
              }

              echo "
              <div class='checkbox-custom' >
                <input type='checkbox' id='check_".$value['id']."' name='repre_".$value['id']."' ".$check." value='1' >
                <label for='check_".$value['id']."' >".$value['nome']."</label>
              </div>
              ";

            }

          ?>
          </div>
        </div>


  </fieldset>
  
  <button type="submit" class="btn btn-primary">Salvar</button>
  <input type="hidden" name="codigo" value="<?=$codigo?>">

</form>