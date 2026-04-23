<link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css"/>

<?php
  if($_base['navegador'] == "Safari"){
  //if(1==2){
?>

              <div>
              <form action="<?=$_base['objeto']?>imagem_manual/codigo/<?=$codigo?>" method="post" enctype="multipart/form-data">
                
                <fieldset>
                  <div class="form-group">
                    <label class="col-md-12">Arquivo</label>
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

                      <div style="text-align:left; padding-top:10px;"><button type="submit" class="btn btn-primary">Enviar</button></div>
                    </div>
                  </div>
                </fieldset>

              </form>
              </div>

<?php } else { ?>

  <form method="post" action="#" role="form">

        <div class="progress">
            <div id="progresso" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0"
                 aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
        </div>

        <div class="form-group row">

            <div class="col-xs-12">

                      <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="input-append">
                          <div class="uneditable-input">
                            <i class="fa fa-file fileupload-exists"></i>
                            <span class="fileupload-preview"></span>
                          </div>
                          <span class="btn btn-default btn-file">
                          <span class="fileupload-exists">Alterar</span>
                          <span class="fileupload-new">Procurar arquivo</span>
                            <input id="imagem" type="file" accept="image/*" multiple />
                          </span>
                          <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remover</a>
                        </div>
                      </div>

                </div>
            </div>

        </div>

  </form>

<?php } ?>

<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>js/canvas-to-blob.min.js"></script>
<script src="<?=LAYOUT?>js/resize.js"></script>
<script>
  
        // Iniciando biblioteca
        var resize = new window.resize();
        resize.init();

        // Declarando variáveis
        var imagens;
        var imagem_atual;
        var nomedaimagem;

        // Quando carregado a página
        $(function ($) {

            // Quando selecionado as imagens
            $('#imagem').on('change', function () {
                enviar();
            });

        });

        /*
         Envia os arquivos selecionados
         */
        function enviar(){

            // Verificando se o navegador tem suporte aos recursos para redimensionamento
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                alert('O navegador não suporta os recursos utilizados pelo aplicativo');
                return;
            }

            // Alocando imagens selecionadas
            imagens = $('#imagem')[0].files;

            // Se selecionado pelo menos uma imagem
            if (imagens.length > 0){
                // Definindo progresso de carregamento
                $('#progresso').attr('aria-valuenow', 0).css('width', '0%');

                // Escondendo campo de imagem
                $('#imagem').hide();

                // Iniciando redimensionamento
                imagem_atual = 0;
                redimensionar();
            }
        }

        /*
         Redimensiona uma imagem e passa para a próxima recursivamente
         */
        function redimensionar(){

            // Se redimensionado todas as imagens
            if (imagem_atual > imagens.length)
            {
                // Definindo progresso de finalizado
                $('#progresso').html('Imagen(s) enviada(s) com sucesso');

                // Limpando imagens
                limpar();

                // Finalizando
                location.reload();
                return;
            }

            // Se não for um arquivo válido
            if ((typeof imagens[imagem_atual] !== 'object') || (imagens[imagem_atual] == null))
            {
                // Passa para a próxima imagem
                imagem_atual++;
                redimensionar();
                return;
            }

            nomedaimagem = imagens[imagem_atual]['name'];

            // Redimensionando
            resize.photo(imagens[imagem_atual], 1200, 'dataURL', function (imagem) {
                                 
                // Salvando imagem no servidor
                $.post('<?=$_base['objeto']?>imagem_redimencionada', {imagem: imagem, codigo: '<?=$codigo?>', nomeimagem: nomedaimagem}, function() {
                    
                    // Definindo porcentagem
                    var porcentagem = (imagem_atual + 1) / imagens.length * 100;

                    // Atualizando barra de progresso
                    $('#progresso').text(Math.round(porcentagem) + '%').attr('aria-valuenow', porcentagem).css('width', porcentagem + '%');

                    // Aplica delay de 1 segundo
                    // Apenas para evitar sobrecarga de requisições
                    // e ficar visualmente melhor o progresso
                    setTimeout(function () {
                        // Passa para a próxima imagem
                        imagem_atual++;
                        redimensionar();
                    }, 1000);

                });

            });
        }
        /*
         Limpa os arquivos selecionados
         */
        function limpar(){
            var input = $("#imagem");
            input.replaceWith(input.val('').clone(true));
        }

</script>