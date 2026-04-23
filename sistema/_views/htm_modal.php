<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<div id="janela_modal_grande" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title modal_titulo" >titulo</h5>
      </div>

      <div class="modal-body" id="modal_conteudo_g" ></div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>

    </div>

  </div>
</div>

<div id="janela_modal_medio" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content" id="conteudo_modal">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title modal_titulo" >titulo</h5>
      </div>
      
      <div class="modal-body" id="modal_conteudo_m"></div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
      
    </div>

  </div>
</div>