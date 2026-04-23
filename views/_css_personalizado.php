<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<style>



	.fonte1{

		font-family: 'Open Sans', sans-serif;

	}

	.fonte2{

		font-family: 'Roboto', sans-serif;

	}



	.main_header{

		position: relative;

		top: 0px;

		z-index: 999999;

		width: 100%;

	}

	.main_header, header{

		background: <?=$_base['cor']['4']?>;

	}

	.fixed_show header{

		background: rgba(0,0,0,0.6) !important;

		display: none;

	}

	.wrapper{ 

	}



	.logo_div{

		width: 100%;

	}

	.logo{

		display: block;

		width: auto;

		height: 75px;

		text-align: left;

		border:none;

		margin-top: 20px;

		margin-bottom: 20px;

	}

	.logo img{

		height: 100%;

	}



	.topo_div1{

		text-align: right;

		width: 100%;

		margin-top: 10px;

	}



	.topo_div1_item{

		height: 100px;

		display: inline-block;

		color: <?=$_base['cor']['1']?>;

		text-align: left;

		margin-top: 20px;

		margin-left: 20px;

	}

	.topo_div1_icos{

		text-align: center;

		float:left;

		font-size:22px;

		width:46px;

		height:46px;

		border-radius:46px;

		background-color: <?=$_base['cor']['1']?>;

		color: <?=$_base['cor']['2']?>;

		padding-top: 12px;

	}

	.topo_div1_icos a{



		font-size:22px;

		width:46px;

		height:46px;

		border-radius:46px;

		background-color: <?=$_base['cor']['1']?>;

		color: <?=$_base['cor']['2']?>;


	}

	.topo_div1_item_txt1{

		font-size: 14px;

		color:#000;

		font-weight: bold;

		color: <?=$_base['cor']['1']?>;

		line-height: 15px;

		padding-left: 12px;

		padding-top: 5px;

		margin-right:30px;

	}

	.topo_div1_item_txt2{

		font-size: 13px;

		color: <?=$_base['cor']['3']?>;

		line-height: 15px;

		padding-left: 12px;

		padding-top: 5px; 

		margin-right: 20px;

	}

	.topo_div1_textos{

		float: left;

	}





	.main_header.type2 header nav ul.menu > li:hover > .sub-nav{

		top:80px !important;

	}

	header nav ul.menu > li > a{

		color:<?=$_base['cor']['64']?>;

		font-size: 17px;

		padding-top:10px;

		font-weight: bold;

	}

	header nav ul.menu > li > a:hover{

		color:<?=$_base['cor']['38']?>;

	}

	header nav {

		float: none;

		text-align:left;

	}





		.topo_redes_sociais{
		text-align: right;
		margin-top: -3px;
	}
	.topo_redes_sociais_item{
		display: inline-block;
		margin:3px;
		width:50px;
		height:50px;
		text-align: center;
	}
	.topo_redes_sociais_item img{
		width: 100%;
	}



	.slider_container{

		margin:0px;

	}

	.sub_banner{

		width: 100%;

	}

	.sub_banner_item{

		width: 25%;

		height: 270px;

		float:left;

		margin:0px;

		border:0px;

		padding:0px;

	}

	.sub_banner_item_txt1{

		margin-top: 15px;

		font-size: 20px;

		color:#fff;

		font-weight: bold;

		text-align: center;

	}

	.sub_banner_item_txt2{

		margin-top: 15px;

		margin-left: 10px;

		margin-right: 15px;

		font-size: 15px;

		color:#fff;

		text-align: center;

	}





	.titulo_padrao{

		text-align: center;

		font-size: 30px;

		color:#000;

		font-weight: 500;

		padding-bottom:0px;

		line-height: 25px;

	}





	.apres_item{

		margin-top:60px;

	}

	.apres_ico{

		float: left;

	}

	.apres_subtitulos{

		float: left;

		font-size:18px;

		font-weight: bold;

		color:#333;

		text-align: left;

		padding-top: 15px;

		padding-left: 15px;

	}

	.apres_subtextos{

		text-align: left;

		margin-top: 20px;

		font-size: 15px;

		color:#000;

	}





	.module_cont{

		padding-bottom:0px;

	}

	.div_numeros{

		width: 100%;

		position: relative;

		padding-top:150px;

		padding-bottom:100px;

		background-image: url(<?=$_base['imagem']['152951244874600']?>);

		background-size: cover;

		background-position: center;

	} 

	.stat_count{

		color:#fff;

		font-size: 50px;

		font-weight: bold;

		padding-bottom:30px;

	}

	.stat_count:before{

		position: absolute;

		top:60px;

		left: 50%;

		margin-left: -50px;

		overflow: hidden;

		width: 100px;

		height:5px;

		content: '\a0'; 

		background-color:#FFF;

	}

	.counter_title{

		color:#fff;

		font-size: 30px;

		font-weight: bold;

		line-height:30px;

	}



	.noticias_div{

		margin-top:30px;

	}

	a.noticias_imagem{

		display: block;

		width: 100%;

		height: 200px;

		background-repeat: no-repeat;

		background-size: cover;

		background-position: center;

	} 

	.noticias_dia{

		text-align:left;

		font-size: 14px;

		color:<?=$_base['cor'][1]?>;

		font-weight: 500;

		padding-top: 5px;

	}

	a.noticias_titulo{

		display: block;

		padding-top: 10px;

		font-size: 17px;

		font-weight: bold;

		text-align: left;

		color: #333;

	}

	.noticias_previa{

		padding-top:5px;

		text-align: left;

		font-size: 14px;

		color:#999;

	}



	.card_item_topo{

		border-radius:40px;

		display: inline-block;

		width: 100%;

		background-color:#dadddc;

		height:40px;

	}

	.card_sub_titu{

		display: inline-block;

		padding-top:8px;

		padding-left: 5px;

		font-weight: bold;

	}

	.state-active{

		background-color:<?=$_base['cor'][1]?>;

		color:<?=$_base['cor'][2]?> !important;

	}

	.state-active .card_sub_titu{

		color:<?=$_base['cor'][2]?> !important;

	}

	.card_item_topo .ico{

		margin: 5px;

		display: inline-block;

	}

	.shortcode_accordion_item_body, .shortcode_toggles_item_body{

		padding-left: 0px;

		padding-top: 7px;

	}



	.galeria_inicial{

		width: 100%;

		position: relative;

		margin-top: 40px;

		padding-top:100px;

		padding-bottom:100px;

		background-image: url(<?=$_base['imagem']['153692954944466']?>);

		background-size: cover;

		background-position: center;

	}



	.equipe_item{

		text-align: center;

		width: 100%;

		height: auto;

		margin-top: 25px;

	}

	.equipe_item_img{

		width: 250px;

		height: 250px;

		border-radius: 170px;

		background-size: cover;

		background-position: center;

		background-repeat: no-repeat;

		display: inline-block;

	}

	.equipe_item_nome{

		font-size: 16px;

		font-weight: 500;

		color:#000;

		text-align: center;

		margin-top:15px;

	}





	.footer{

		background-color: <?=$_base['cor']['60']?>;

	}

	.copyright{

		color: <?=$_base['cor']['63']?>;

		text-align: center;

		width: 100%;

		padding-bottom: 20px;

	}

	.footer h3{

		color:<?=$_base['cor']['62']?>;

	}

	.rodape_textos{

		color:<?=$_base['cor']['63']?>;

	}



	.redessociais_rodape{
		width: 65px;
		display: inline-block;
		margin-right:5px;
		margin-top: 10px;
	}



	.rodape_menu{

		list-style: none;

		text-align: left;

		margin: 0px;

		padding: 0px;

		margin-bottom:50px;

	}

	.rodape_menu li{

		list-style: none;

		width: 40%;

		margin-right: 10%;

		float: left;

		margin-top: 10px;

		text-align: left;

	}

	.rodape_menu li a{

		font-size: 15px;

		color:<?=$_base['cor']['62']?>;

	}

	.rodape_menu li a:hover{

		text-decoration: underline;

	}



	input[type="text"], input[type="email"], input[type="password"], textarea{

		border-radius: 2px;

		-webkit-border-radius: 2px;

		width: 100%;

		height: 35px;

	}



	.botao_news{

		padding-left: 20px;

		padding-right: 20px;

		padding-top:7px;

		padding-bottom:5px;

		text-align: center;

		background-color:<?=$_base['cor']['1']?>; 

		color:<?=$_base['cor']['2']?>;

		cursor: pointer;

		font-size: 12px;

		font-weight: 500;

		border:0px;

		border-radius: 2px;

	}

	.botao_news:hover{

		background-color:<?=$_base['cor']['3']?>; 

		color:<?=$_base['cor']['4']?>;

	}





	@media (max-width:1200px){



		.apres_item{

			margin-top:40px;

		}

		.apres_subtitulos{

			float: left;

			font-size:17px;

			font-weight: bold;

			color:#333;

			text-align: left;

			padding-top: 15px;

			padding-left: 15px;

		}

		.apres_subtextos{

			margin-top:15px;

			font-size: 14px;

		}



	}



	@media (max-width:990px){ 



		.topo_redes_sociais{

			text-align: center;

			width: 100%;

			padding-bottom:20px;

		}



		.topo_div1_item{

			height: 100px;

			display: inline-block;

			color: <?=$_base['cor']['1']?>;

			text-align: left;

			margin-top: 20px;

			margin-left: 20px;

		}

		.topo_div1_icos{

			text-align: center;

			float:left;

			font-size:20px;

			width:35px;

			height:35px;

			border-radius:35px;

			background-color: <?=$_base['cor']['1']?>;

			color: <?=$_base['cor']['2']?>;

			padding-top: 6px;

		}

		.topo_div1_item_txt1{

			font-size: 13px;

			color:#000;

			font-weight: bold;

			color: <?=$_base['cor']['1']?>;

			line-height: 15px;

			padding-left: 12px;

			padding-top: 5px;

			margin-right:10px;

		}

		.topo_div1_item_txt2{

			font-size: 12px;

			color: <?=$_base['cor']['3']?>;

			line-height: 15px;

			padding-left: 12px;

			padding-top: 5px; 

			margin-right:10px;

		}



		.sub_banner_item{

			width: 25%;

		}

		.sub_banner_item_txt1{

			margin-top: 15px;

			font-size:16px; 

		}

		.sub_banner_item_txt2{

			margin-top: 10px;

			margin-left: 10px;

			margin-right: 10px;

			font-size:13px;

			line-height: 20px;

		}



		.titulo_padrao{

			font-size: 26px;

		}



		.apres_ico{

			text-align: center;

			width: 100%;

		}

		.apres_subtitulos{

			text-align: center;

			padding-left: 0px;

			width: 100%;

		}

		.apres_subtextos{

			text-align: center;

		}



		.div_numeros{

			margin-top:50px;

		}



	}



	@media (max-width:767px){ 



		.logo{

			height: 60px;

		}



		.topo_div1{

			text-align: center;

			width: 100%;

			margin-top: 20px;

		}

		.topo_div1_item{

			margin:0px;

			width:32%;

			text-align: center;

		}

		.topo_div1_icos{

			text-align: center;

			width: 100%;

		}

		.topo_div1_item_txt1{

			text-align: center;

			width: 100%;

			padding-left: 0px;

			font-size: 12px;

		}

		.topo_div1_item_txt2{

			text-align: center;

			width: 100%;

			padding-left: 0px;

			font-size: 11px;

		}

		.topo_div1_textos{

			width: 100%;

		}



		a.tagline_toggler{

			display: none;

		}

		a.menu_toggler{

			left: 100%;

			margin-left: -50px;

		}

		.titulo_padrao{

			font-size: 25px;

		}



		.sub_banner_item{

			width:50%;

		}

		.sub_banner_item_txt1{

			margin-top: 15px;

			font-size:16px; 

		}

		.sub_banner_item_txt2{

			margin-top: 10px;

			margin-left: 10px;

			margin-right: 10px;

			font-size:13px;

			line-height: 20px;

		}



		.rodape_menu li{

			width: 90%;

		}



	}













	.blog_nenhumresultado{

		font-size: 18px;

		color:#666;

		text-align: center;

		padding-top: 80px;

		padding-bottom: 100px;

	}

	.blog_lista_titulo {

		line-height:18px !important;

		padding-bottom:10px;

		margin-bottom: 0px;

	}

	.blog_lista_previa{

		line-height:1.5 !important;

		padding-top:15px;

	}

	.blog_lista_divisao{

		margin-bottom:25px;

	}

	.blog_lista_divisao hr {

		border-top: 1px solid #ddd;

	}



	.paginacao{

		padding-top:10px;

		margin-bottom:30px;

		text-align: center;

		width: 100%;

	}

	.paginacao ul{

		list-style: none;

	}

	.paginacao li{

		list-style: none;

		display: inline-block;

		text-align: center;

		padding:0px;

		margin: 2px;

	}

	.paginacao a{

		border-radius: 2px;

		font-size: 15px;

		padding: 5px;

	}

	.paginacao a:active, .paginacao a.active {

		background: <?=$_base['cor']['1']?>;   

		border-color: <?=$_base['cor']['1']?>;

		color: <?=$_base['cor']['2']?>;

	}

	.paginacao a:active:hover, .paginacao a.active:hover {

		background: <?=$_base['cor']['3']?>;   

		border-color: <?=$_base['cor']['3']?>;

		color: <?=$_base['cor']['4']?>;

	}



	.blog_imagem_interna_mini {

		margin-top:7px;

		margin-right:7px;

		width: 100px;

		height: 70px;

		overflow: hidden;

		display: inline-block;

		text-align: center;

	}

	.blog_imagem_interna_mini:hover {

		opacity: 0.8;

	}

	.blog_imagem_interna_mini img{

		height: 100%;

		min-width: 100%;

		border:0px;

	}



	.blog_categorias li{

		padding-top: 10px;

		font-size: 16px;

	}

	.blog_categorias li a{

		color:<?=$_base['cor']['1']?>;

	}

	.blog_categorias li a:hover{

		color:<?=$_base['cor']['3']?>;

	}

	.blog_categorias li a.active{

		color:<?=$_base['cor']['3']?>;

	}

	.blog_categorias li a.active:hover{

		color:<?=$_base['cor']['3']?>;

	}



	ul.blog_lista_meta {

		list-style: none;

		margin-left: 0px;

		padding-left: 0px;

		margin-bottom: 15px;

		margin-top: 0px;

		font-family: 'Roboto', sans-serif;

	}

	ul.blog_lista_meta li {

		display: inline-block;

		padding-right: 25px;

		list-style: none;

		margin-left: 0px;

		padding-left: 0px;

		font-size: 13px;

	}







	.no_ar_logo{

		text-align: left;

	}

	.no_ar_logo img{

		width: 160px;

	}

	.no_ar_programa{

		font-size: 22px;

		font-weight: bold;

		color:#fff;

		margin-top: 20px;

	}

	.no_ar_apresentador{

		font-size:15px;

		font-weight: 500;

		color:#fff500;

		font-style: italic;

		margin-top: 0px;

		padding-top:0px;

	}

	.no_ar_descricao{

		font-size: 16px; 

		color:#fff;

		margin-top:15px;

	}







	.programacao_dias_semana{

		width: 100%;

		border:1px solid #999; 

		margin-top:60px;

		background-color: #ddd;

		color:#000;

		text-align: center;

	}

	.dias_semana_item{

		text-align: center;

		padding-top: 10px;

		padding-bottom: 10px;

		display: inline-block;

		width: 130px;

		color:#000;

		font-size: 15px;

		font-weight: bold;

		text-transform: uppercase;

		cursor:pointer;

	}

	.dias_semana_item:hover{

		background-color: #fff;

		color:#000;

	}

	.dias_semana_item_ativo{

		background-color: <?=$_base['cor']['1']?>;

		color:<?=$_base['cor']['2']?>;

	}



	.lista_dia{

		margin-top: 30px;

		width: 100%;

		height: auto;

		margin-bottom:100px;

	}



	.prog_linha{

		margin-top: 2px;

		background-color: #fff;

	}



	.prog_td_titulo{

		padding: 10px;

		text-align: center;

		font-size: 13px;

		font-weight: bold;

		color:#000;

		background-color: #fff;

	}

	.prog_td_linha{

		padding: 5px;

		text-align: left;

		font-size: 14px;

		font-weight:500;

		color:#666;

		border:1px solid #000;

	}

	.footer_bottom{

		padding-bottom:80px !important;

	}



	.info_radio_texto{

		font-size: 14px;

		color: #fff;

		text-align: center;

		display: inline-block;

		font-weight: bold; 

		padding-top:15px;

		margin-right: 15px;

		float: left;

	}

	.play_radio{

		display: inline-block;

		padding-top:10px; 

		float: left;

	}

	.play_radio img{

		width:45px;

	}

	

	.info_radio{

		font-size: 14px;

		color: #fff;

		text-align: center;

		display: inline-block; 

		padding-top: 10px;

		margin-right:15px;

		float: left;

	}

	.info_radio_programa{

		font-size: 14px;

		color: #A3D900;

		text-align: left;

		display: block;

		line-height:14px;

		font-weight: bold;		 

	}

	.info_radio_apresentador{

		font-size: 12px;

		line-height:14px;

		color: #fff;

		text-align: left;

		display: block;

		margin-top: 3px;

	}  

  

	.volume_div{

		display: none;

	}

	.player_radio{

		float: left;

	}

	.volume_radio{

		float: left

	}

	.auto_falante{

		float: left;

	}

	.mute_radio{

		float: left;

	}





/*! Divino Silva divinosilva.com.br */
.btn-radio {
		position: fixed;
		bottom: 100px;
		left: 1em;
		z-index: 999999999;
		display: block
}
.btn-radio {
		position: fixed;
		bottom: 100px;
		right: 1em;
		z-index: 999999999;
		display: block
}

.btn-whats {
	position: fixed;
	bottom: 100px;
	right: 1em;
/*! left: 1em;  */
	z-index: 999999999;
	display: block
}
button.pulse-button i {
	color: #ffffff;
	font-size: 25px;
}
button.pulse-button:focus {
	outline: none;
}
.pulse-button {
	position: relative;
	width: 51px;
	height: 51px;
	border: none;
	box-shadow: 0 0 0 0 rgba(255, 102, 0, 0.29);
	border-radius: 50%;
	background: #FF6600;
	background-image: -webkit-linear-gradient(top, #FF6600, #FF6600);
	background-image: -moz-linear-gradient(top, #FF6600, #FF6600);
	background-image: -ms-linear-gradient(top, #FF6600, #FF6600);
	background-image: -o-linear-gradient(top, #FF6600, #FF6600);
	background-image: linear-gradient(to bottom, #FF6600, #FF6600);
	background-size: cover;
	background-repeat: no-repeat;
	cursor: pointer;
	-webkit-animation: pulse 1.25s infinite cubic-bezier(0.16, 0, 0, 1);
	-moz-animation: pulse 1.25s infinite cubic-bezier(0.16, 0, 0, 1);
	-ms-animation: pulse 1.25s infinite cubic-bezier(0.16, 0, 0, 1);
	animation: pulse 1.25s infinite cubic-bezier(0.16, 0, 0, 1);
}
.pulse-button:hover {
	-webkit-animation: none;
	-moz-animation: none;
	-ms-animation: none;
	animation: none;
}
@-webkit-keyframes pulse {
	to {
		box-shadow: 0 0 0 45px rgba(255, 102, 0, 0);
	}
}
@-moz-keyframes pulse {
	to {
		box-shadow: 0 0 0 45px rgba(255, 102, 0, 0);
	}
}
@-ms-keyframes pulse {
	to {
		box-shadow: 0 0 0 45px rgba(255, 102, 0, 0);
	}
}
@keyframes pulse {
	to {
		box-shadow: 0 0 0 45px rgba(255, 102, 0, 0);
	}
	
	.gallery_item_wrapper:hover {
  transform: scale(1.02);
  transition: transform 0.3s ease;
}

.block_fade {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery_item_wrapper:hover .block_fade {
  opacity: 1;
}
}
</style>
