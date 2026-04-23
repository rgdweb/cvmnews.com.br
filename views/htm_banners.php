<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<div class="fw_block bg_start wall_wrap">
  <div class="row">
    <div class="col-sm-12 first-module module_slider module_cont pb0 light_parent">
      <div class="slider_container">
        <div class="fullscreen_slider">
          <ul>
            <?php
            
            foreach ($banners as $key => $value) {
              
              echo '
              <li data-transition="fade" data-slotamount="5" data-masterspeed="600" >              
              <img src="'.$value['imagem'].'" data-bgposition="center top" data-bgrepeat="no-repeat" /> 
              </li>
              ';

            }
            
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <?php if($controller == 'index') { ?>
  <?php } ?>
</div>
