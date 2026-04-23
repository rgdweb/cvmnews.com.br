<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set("Brazil/East");

// Carrega arquivo de configuração
require_once('../_config.php');

// Define constantes para tokens, banco, caminhos, etc
define('TOKEN1', $config['token2']); // Atenção: corrigido nome para TOKEN1
define('TOKEN2', md5(TOKEN1 . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
define("SERVIDOR", $config['SERVIDOR']);
define("USUARIO", $config['USUARIO']);
define("SENHA", $config['SENHA']);
define("BANCO", $config['BANCO']);

$config_dominio = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
if (!empty($config['PASTA'])) {
    $config_dominio .= $config['PASTA'] . "/";
}
define("DOMINIO", $config_dominio . "sistema/");
define("PASTA_CLIENTE", $config_dominio . "sistema/arquivos/");
define("AUTOR", "publiquesites.com.br");
define("TITULO_VIEW", "Gerenciador de Conteúdos - PUBLIQUE SITES");

define("CONTROLLERS", "_controllers/");
define("VIEWS", "_views/");
define("MODELS", "_models/");
define("LAYOUT", DOMINIO . VIEWS);
define("FAVICON", LAYOUT . "img/favicon.png");
define("FORCAR_SSL", $config['ssl']);

// Inclui classes base manualmente — importante para evitar erro "class not found"
require_once("_system/system.php");
require_once("_system/mysql.php");
require_once("_system/controller.php");
require_once("_system/model.php");

// Autoloader para carregar automaticamente outras classes de modelos (ex: model_usuario.php)
function auto_carregador($arquivo) {
    $arquivo_path = MODELS . $arquivo . ".php";
    if (file_exists($arquivo_path)) {
        require_once($arquivo_path);
    } else {
        echo "Erro: um arquivo ($arquivo) do sistema não foi encontrado!";
        exit;
    }
}
spl_autoload_register("auto_carregador");

// Inicializa sistema
$start = new system();
$start->run();
