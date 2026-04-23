<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<div class="footer">
  <div class="container">
    <div class="pre_footer">
      <div class="row">

        <div id="contato" class="col-xs-12 col-sm-4 contato-destaque">
          <h3>Fale Conosco</h3>

          <div>
            <div style="float:left; width:50px; text-align: center; font-size:40px; margin-right:15px;">
              <i class="far fa-envelope"></i>
            </div>
            <div style="float: left;">
              <div class="rodape_textos" style="padding-top: 10px;"><?=$_base['rodape_email']['conteudo']?></div>
            </div>
            <div style="clear:both;"></div>
          </div>

          <div style="margin-top:15px;">
            <div style="float:left; width:50px; text-align: center; font-size:40px; margin-right:15px;">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <div style="float: left;">
              <div class="rodape_textos" style="margin-top:-5px;"><?=$_base['rodape_contato1']['conteudo']?></div>
              <div class="rodape_textos" style="padding-top:5px;"><?=$_base['rodape_contato2']['conteudo']?></div>
            </div>
            <div style="clear:both;"></div>
          </div>

          <div style="margin-top:15px;">
            <div style="float:left; width:50px; text-align: center; font-size:35px; margin-right:15px;">
              <i class="fas fa-map-marked-alt"></i>
            </div>
            <div style="float: left;">
              <div class="rodape_textos" style="margin-top:10px;"><?=$_base['rodape_endereco']['conteudo']?></div>
            </div>
            <div style="clear:both;"></div>
          </div>

          <div style="padding-bottom: 50px; padding-top: 30px; display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">
            <!-- Redes Sociais -->
            <div style="display: flex; gap: 10px;">
              <?php
              $listaredes = $_base['listaredes'];
              foreach ($listaredes as $key => $value) {
                echo "
                <div class='redessociais_rodape'>
                  <a href='".$value['endereco']."' target='_blank'>
                    <img src='".$value['imagem']."'>
                  </a>
                </div>
                ";
              }
              ?>
            </div>

            <!-- Compartilhamento -->
            <div class="share-container">
              <span class="share-label">Compartilhe:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode('http://'.$_SERVER['HTTP_HOST'])?>" target="_blank" class="share-btn facebook" title="Facebook">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="https://wa.me/?text=<?=urlencode('Confira este site: http://'.$_SERVER['HTTP_HOST'])?>" target="_blank" class="share-btn whatsapp" title="WhatsApp">
                <i class="fab fa-whatsapp"></i>
              </a>
              <a href="https://www.instagram.com/" target="_blank" class="share-btn instagram" title="Instagram" onclick="alert('Link copiado! Abra o Instagram e cole na biografia ou stories.')">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="https://x.com/intent/tweet?url=<?=urlencode('http://'.$_SERVER['HTTP_HOST'])?>&text=<?=urlencode('Confira este site!')?>" target="_blank" class="share-btn x-twitter" title="X">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </a>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-4">
          <h3>Acesso Rápido</h3>
          <ul class="rodape_menu">
            <?php foreach ($_base['menu_rodape'] as $key => $value) {
              echo "<li><a href='".$value['destino']."'>".$value['titulo']."</a></li>";
            } ?>
          </ul>
        </div>

        <div class="col-xs-12 col-sm-4">
          <h3>Receba Informações</h3>
          <div class="input-field">
            <input type="text" name="news_nome" id="news_nome" class="news_form" placeholder="Digite seu Nome" />
          </div>
          <div class="input-field" style="text-align: right;">
            <input type="text" name="news_email" id="news_email" class="news_form" placeholder="Digite seu endereço de e-mail" />
            <button onClick="abre_cadastro_news();" class="botao_news">ENVIAR</button>
          </div>
        </div>

      </div>
    </div>

    <a class="logo" href="<?=DOMINIO?>"><img alt="<?=$_base['titulo_pagina']?>" src="<?=$_base['imagem']['147129831543478']?>" /></a>
    <footer class="main-footer">
        <div class="pull-right hidden-xs"></div>
        <span class="copyright"><strong>Copyright &copy; <?=date('Y')?> </strong> <?=$_base['copy']['conteudo']?> <a href="https://www.facebook.com/marciogomes2023/" target="_blank">Márcio Gomes</a></span>
    </footer>
    <div class="footer_bottom">
      <div class="copyright"></div>
    </div>
  </div>
</div>

<!-- Botões flutuantes -->
<div class="btn-radio">
  <a href="javascript:newPopup();">
    <button id="draggable" class="pulse-button"><i class="fa fa-play"></i></button>
  </a>
</div>

<div class="btn-whats">
  <a href="https://wa.me/<?=$_base['radio_whatsapp']?>?text=Olá! Estou ouvindo <?=$_base['titulo_pagina']?>, quero fazer um pedido 🙂" target="_blank">
    <button id="draggable" class="pulse-button"><i class="fab fa-whatsapp"></i></button>
  </a>
</div>


<!-- Estilo dos botões -->
<style>
.share-container {
  display: flex;
  gap: 10px;
  align-items: center;
  margin: 20px 0;
  flex-wrap: wrap;
}
.share-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  text-decoration: none;
  color: white;
  font-size: 18px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.share-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  filter: brightness(1.2);
}
.facebook { background: #1877f2; }
.whatsapp { background: #25d366; }
.instagram { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
.x-twitter { background: #1DA1F2; }
.share-label {
  font-weight: bold;
  color: white;
  margin-right: 10px;
}

/* Responsivo para celular */
@media (max-width: 600px) {
  .share-container {
    flex-direction: column;
    align-items: flex-start;
  }
  .share-btn {
    width: 100%;
    max-width: 240px;
    margin-bottom: 8px;
    font-size: 16px;
  }
  .share-label {
    margin-bottom: 12px;
    display: block;
  }
}

/* Footer bottom estilizado */
.footer_bottom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 20px;
    text-align: center;
    margin-top: 50px;
    box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}
.footer_bottom::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
    background-size: 300% 100%;
    animation: gradientShift 3s ease infinite;
}
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.main-footer .copyright {
    font-family: 'Arial', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    letter-spacing: 1px;
}
.main-footer .copyright a {
    color: #ffd700;
    text-decoration: none;
    font-weight: bold;
}
.main-footer .copyright a:hover {
    text-decoration: underline;
}
.btn-radio, .btn-whats {
  position: fixed;
  z-index: 1000;
  max-width: 60px;
}
.btn-radio {
  right: 20px;
  bottom: 100px;
}
.btn-whats {
  right: 20px;
  bottom: 160px;
}
@media (max-width: 768px) {
  .main-footer .copyright { font-size: 14px; }
}
</style>

<!-- Scripts -->
<script>
function abre_cadastro_news() {
  var nome = document.getElementById('news_nome').value;
  var email = document.getElementById('news_email').value;
  modal('<?=DOMINIO?>cadastrar_email/inicial/email/' + email + '/nome/' + nome);
}
function newPopup() {
  window.open(
    '<?= $_base['radio_porta'] ?>',
    'pagina',
    "width=500, height=300, top=200, left=250, scrollbars=no"
  );
}
</script>
