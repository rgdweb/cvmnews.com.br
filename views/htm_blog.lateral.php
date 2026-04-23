<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<div>

	<form id="form_busca" name="form_busca" action="<?=DOMINIO?>busca_blog" method="post" >
		
		<div style="margin-top:30px;">
			<input name="busca" type="text" class="form-control campo_busca" placeholder="O que procura?" style="width:100%; border-radius:2px;" >
		</div>

		<div>
			<input type="button" value="BUSCAR" class="botao_padrao" style="font-size: 15px; border-radius: 2px; width: 100%;" onclick="document.form_busca.submit()" >
		</div> 

	</form>

</div>

<div style="margin-top: 30px;">
	
	<div class="titulo_padrao" style="position: relative; margin-bottom: 20px;" >OUTRAS NOTÍCIAS</div>

	<div class="blog_categorias" >
		<ul class="blog_lista_meta">
		<?php

		foreach ($categorias as $key => $value) {
			
			$endereco_cat = DOMINIO."blog/lista/categoria/".$value['codigo'].'#corpo';

			if($categoria_codigo == $value['codigo']){
				$active = "  class='active' ";
			} else {
				$active = "";
			}
			
			echo "
			<li>
			<a href='$endereco_cat' $active > ".$value['titulo']."</a>
			</li>
			";
			
		}

		?>
	</ul>
	</div>
	
</div>