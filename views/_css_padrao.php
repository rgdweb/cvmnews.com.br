<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<style>
body,
input[type="text"],
input[type="email"],
input[type="password"],
input[type="submit"],
textarea {
	font-family: 'Roboto', sans-serif;
}

.logo_retina,
.logo_retina {
	width:176px;
	height:33px;
}

::selection {
	background:<?=$_base['cor']['1']?>;
	color:#ffffff;
}
::-moz-selection {
	background:<?=$_base['cor']['1']?>;
	color:#ffffff;
}

a {
	color:<?=$_base['cor']['1']?>;
}

.color {
	color:<?=$_base['cor']['1']?> !important;
}

header nav ul.menu > li > a:before {
	background:<?=$_base['cor']['1']?>;
}

header nav ul.sub-menu > li:hover > a,
header nav ul.sub-menu > li.current-menu-item > a,
header nav ul.sub-menu > li.current-menu-parent > a {
	color:<?=$_base['cor']['1']?>;
}

.main_header.type4 header nav ul.menu > li:hover > a,
.main_header.type4 header nav ul.menu > li.current-menu-ancestor > a,
.main_header.type4 header nav ul.menu > li.current-menu-item > a,
.main_header.type4 header nav ul.menu > li.current-menu-parent > a {
	color:<?=$_base['cor']['1']?>;
}

.highlighted_colored {
    background:<?=$_base['cor']['1']?>;
}

.dropcap.type2 {
	color:<?=$_base['cor']['1']?>;
}

.dropcap.type5 {
	background:<?=$_base['cor']['1']?>;
}

blockquote.type2:before {
	color:<?=$_base['cor']['1']?>;
}

blockquote.type5:before {
	background:<?=$_base['cor']['1']?>;
}

.module_content ul.type2 li:before {
    color:<?=$_base['cor']['1']?>;
}

.sidepanel a:hover {
	color:<?=$_base['cor']['1']?>;
}

.recent_posts li a.title:hover,
.product_posts li a.title:hover,
.pre_footer .recent_posts li a.title:hover,
.pre_footer .product_posts li a.title:hover,
.star_rating,
.subtotal span {
	color:<?=$_base['cor']['1']?>;
}

#mc_signup_submit:hover {
    background: <?=$_base['cor']['1']?> !important;
}

.tweet_module ul li a:hover,
.pre_footer .tweet_module ul li a:hover {
	color:<?=$_base['cor']['1']?>;
}

.shortcode_button.btn_type5,
.shortcode_button.btn_type4:hover,
.shortcode_button.btn_type4.dark_parent:hover {
    background: <?=$_base['cor']['1']?>;
	color:#fff;
	border-color:<?=$_base['cor']['1']?>;
}

.shortcode_button.btn_type5:hover {
    background: #ff6667;
}

.map_collapse:hover {
	background: #ff6667;
}

.stat_count {
	color:<?=$_base['cor']['1']?>;
}

.counter_icon {
	background:<?=$_base['cor']['1']?>;	
}

.skill_div {
	background: <?=$_base['cor']['1']?>;
}

.iconbox_wrapper .ico {
	background: <?=$_base['cor']['1']?>;
}

.color_gradient_vert,
h5.shortcode_accordion_item_title:hover .ico:after,
h5.shortcode_toggles_item_title:hover .ico:after,
h5.shortcode_accordion_item_title.state-active .ico:after,
h5.shortcode_toggles_item_title.state-active .ico:after,
.icon5,
.icon6,
.icon7,
.pagerblock li a.current,
.pagerblock li a.current:hover,
.pagerblock li span,
.quantity .minus:hover,
.quantity .plus:hover,
a.remove:hover {
	background: <?=$_base['cor']['1']?>; /* Old browsers */
	background: -moz-linear-gradient(top,  <?=$_base['cor']['1']?> 0%, #ff8164 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?=$_base['cor']['1']?>), color-stop(100%,#ff8164)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* IE10+ */
	background: linear-gradient(to bottom,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_base['cor']['1']?>', endColorstr='#ff8164',GradientType=0 ); /* IE6-9 */
}

.color_gradient_hor,
.widget_filter .ui-slider-range {
	background: <?=$_base['cor']['1']?>; /* Old browsers */
	background: -moz-linear-gradient(left,  <?=$_base['cor']['1']?> 0%, #ff8164 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, right top, color-stop(0%,<?=$_base['cor']['1']?>), color-stop(100%,#ff8164)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(left,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(left,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(left,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* IE10+ */
	background: linear-gradient(to right,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_base['cor']['1']?>', endColorstr='#ff8164',GradientType=1 ); /* IE6-9 */
}

/* Iconbox Gradient */
a:hover .iconbox_wrapper .ico:after,
.shortcode_iconbox.type4 .iconbox_wrapper .ico:after,
.shortcode_iconbox.type5 .iconbox_wrapper .ico:after,
.step_by_step .iconbox_wrapper .ico:after,
.shortcode_iconbox.type6 .iconbox_wrapper .ico:after {
	background: <?=$_base['cor']['1']?>; /* Old browsers */
	background: -moz-linear-gradient(top,  <?=$_base['cor']['1']?> 0%, #ff8164 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?=$_base['cor']['1']?>), color-stop(100%,#ff8164)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* IE10+ */
	background: linear-gradient(to bottom,  <?=$_base['cor']['1']?> 0%,#ff8164 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_base['cor']['1']?>', endColorstr='#ff8164',GradientType=0 ); /* IE6-9 */
}

.easyPieChart {
    color: <?=$_base['cor']['1']?>;
}

a:hover .iconbox_wrapper .ico:before {
	box-shadow:0 0 0 2px <?=$_base['cor']['1']?>;	
}

h1.light span {
	color: <?=$_base['cor']['1']?>;
}

.slick_testim_info h5 span,
.testimonials_list li .item h5.testimonials_title span {
	color: <?=$_base['cor']['1']?>;
}

h5.shortcode_accordion_item_title:hover,
h5.shortcode_toggles_item_title:hover,
h5.shortcode_accordion_item_title.state-active,
h5.shortcode_toggles_item_title.state-active {
   color:<?=$_base['cor']['1']?>;
}

.module_team .item_list.type2 .teamlink:hover {
    color: <?=$_base['cor']['1']?> !important;
}

.module_cont hr.type2 {
    border-top: <?=$_base['cor']['1']?> 1px solid;
}

.price_item.most_popular .price_item_title h5 {
   color: <?=$_base['cor']['1']?>;
}

.shortcode_tab_item_title.active:before {
    background: <?=$_base['cor']['1']?>;
}

.item_tab h6 a:hover,
.contact_info_item a:hover,
.shortcode_timeline a:hover {
	color:<?=$_base['cor']['1']?>;
}

.shortcode_timeline_date {
	background: <?=$_base['cor']['1']?>;
}

.view_link:hover,
.view_link:hover i {
	color:<?=$_base['cor']['1']?>;
}

.featured_items_body a:hover {
	color:<?=$_base['cor']['1']?>;
}

.breadcrumbs a:hover {
	color:<?=$_base['cor']['1']?>;
}

.map_collapse {
	background: <?=$_base['cor']['1']?>;
}

.table_info_title h3 .badge {
	background: <?=$_base['cor']['1']?>;
}

.send_mail h3 a {
	color:<?=$_base['cor']['1']?>;
}

input[type="button"],
input[type="reset"],
input[type="submit"],
.coupon input[type="submit"]:hover {
	background-color:<?=$_base['cor']['1']?>;
}

input[type="button"]:hover,
input[type="reset"]:hover,
input[type="submit"]:hover {
    background-color:#ff6667;
}

.with_reset .fright:hover:before {
	color:<?=$_base['cor']['1']?>;
}

.widget_tag_cloud a:hover {
    color:<?=$_base['cor']['1']?>;
	border-color:<?=$_base['cor']['1']?>;
}

.countdown-amount {
	color:<?=$_base['cor']['1']?>;
}

.global_count_wrapper.horizontal .count_title h1 span {
    color:<?=$_base['cor']['1']?>;
}

.blogpost_title a:hover,
.listing_meta a:hover {
	color:<?=$_base['cor']['1']?>;
}

.blog_post_preview blockquote:before {
	color:<?=$_base['cor']['1']?>;
}

.pagerblock li a:hover {
	color:<?=$_base['cor']['1']?>;
}

.blogpost_user_meta h3 a,
.prev_next_links a:hover,
.comment_author_name a:hover,
.comment_meta a:hover {
	color:<?=$_base['cor']['1']?>;
}

.contact_info a:hover {
	color:<?=$_base['cor']['1']?>;	
}

.widget_filter #slider-range .ui-slider-handle:before {
	background:<?=$_base['cor']['1']?>;
}

.widget_filter #slider-range .ui-slider-handle:after {
	border-top: 4px solid <?=$_base['cor']['1']?>;
}

.sidepanel li.current-menu-item a {
	color:<?=$_base['cor']['1']?>;
}

.item_cart:hover,
.item_link:hover,
.shop_list_info a:hover,
.posted_in a:hover,
.tagged_as a:hover,
.product-name a:hover,
.calculate:hover,
h2.portf_title a:hover {
	color:<?=$_base['cor']['1']?>;
}

.filter_navigation ul li ul li a:hover {
	color:<?=$_base['cor']['1']?>;
	border-color:<?=$_base['cor']['1']?>;
}

.filter_navigation ul li ul li a:before {
	background-color:<?=$_base['cor']['1']?>;
}

.slide_title span {
	color:<?=$_base['cor']['1']?>;
}

.slide_btn a {
	background-color:<?=$_base['cor']['1']?>;
	border-color:<?=$_base['cor']['1']?>;
}

.news_block .img_block:before {
	border:1px <?=$_base['cor']['1']?> solid;
}

.proj_title h5 a:hover,
.proj_meta a:hover {
	color:<?=$_base['cor']['1']?>;
}

.page_has_countdown .count_title h1 {
    color:<?=$_base['cor']['1']?>;
}

.color_bg {
    background-color:<?=$_base['cor']['1']?>;
}

.slide_btn a.light_parent:hover,
.light_parent .slide_btn a:hover {
	background-color:#ff6667;
	border-color:#ff6667;
}

.table_info_details,
.table_info_details:focus {
	color:<?=$_base['cor']['1']?>;
}

.mobile_menu_wrapper a:hover,
.mobile_menu_wrapper .current-menu-parent a,
.mobile_menu_wrapper .current-menu-parent .sub-menu a:hover,
.mobile_menu_wrapper .current-menu-parent .sub-menu li.current-menu-parent a.mob_link {
	color:<?=$_base['cor']['1']?>;
}

.mobile_menu_wrapper .current-menu-item a.mob_link {
	color:<?=$_base['cor']['1']?> !important;
}

.mobile_menu_wrapper li.current-menu-parent.menu-item-has-children:before,
.mobile_menu_wrapper li.menu-item-has-children:hover:before {
	color:<?=$_base['cor']['1']?>;
}

.login_popup .forgot_password a:hover {
	color:<?=$_base['cor']['1']?>;
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

</style>