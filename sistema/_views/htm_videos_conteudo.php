<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?=$_titulo?></title>
  <link rel="stylesheet" href="<?=LAYOUT?>css/bootstrap.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
  <style>
    .gallery_item { width: 23%; margin: 1%; float: left; box-sizing: border-box; }
    .gallery_item img { width: 100%; }
    .video_titulo { font-weight: bold; color: #333; margin-top: 10px; }
    @media(max-width: 768px){ .gallery_item { width: 48%; } }
    @media(max-width: 480px){ .gallery_item { width: 100%; } }
  </style>
</head>
<body>

<div class="container" style="margin-top:30px;">

  <h2 style="margin-bottom:20px;"><i class="fa fa-video-camera"></i> Galeria de Vídeos</h2>

  <div class="video_gallery">
    <?php
    if(isset($lista_videos) && count($lista_videos) > 0){
      foreach($lista_videos as $video){
        if(strlen($video['video']) < 20 && !str_contains($video['video'], '<')){
          $id = trim($video['video']);
          $titulo = $video['titulo'];
          $thumbnail = "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
          $url = "https://www.youtube.com/watch?v={$id}";

          echo "
          <div class='gallery_item'>
            <a href='$url' class='video-popup'><img src='$thumbnail' alt='$titulo'></a>
            <div class='video_titulo'>$titulo</div>
          </div>";
        }
      }
    } else {
      echo "<p>Nenhum vídeo encontrado.</p>";
    }
    ?>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?=LAYOUT?>js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script>
$(document).ready(function(){
  $('.video_gallery').magnificPopup({
    delegate: 'a.video-popup',
    type: 'iframe',
    gallery: { enabled:true }
  });
});
</script>
</body>
</html>
