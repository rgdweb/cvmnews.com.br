<script type="text/javascript">
	(function () {
		var options = {
 whatsapp: "<?=$_base['chat_fone']?>", // Número do WhatsApp
 company_logo_url: "<?=$_base['logo']?>", // URL com o logo da empresa
 greeting_message: "Olá, tudo bem com você? Fale conosco agora pelo WhatsApp!", // Texto principal
 call_to_action: "Olá, tudo bem com você? Fale conosco agora pelo WhatsApp!", // Chamada para ação
 position: "right", // Posição do widget na página 'right' ou 'left'
};
var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
})();
</script>