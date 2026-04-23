<?php
class system {

    private $_url;
    private $_explode;
    protected $_controller;
    protected $_action;
    protected $_params;

    public function __construct(){
        $this->setUrl();
        $this->setExplode();
        $this->setController();
        $this->setAction();
        $this->setParams();        
    }
    
    private function setUrl() {
        $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
        $url = $url ?: 'index';
        $url = trim($url);
        $url = str_replace(["<", ">", "\\", "=", "?", "#"], '', $url);
        $url = strip_tags($url);
        if(in_array($url, ['inicial', 'inicial/'])) $url = 'index';
        $this->_url = $url;
    }

    private function setExplode(){
        $this->_explode = explode('/', $this->_url);
    }

    private function setController(){
        $this->_controller = $this->_explode[0];
    }

    private function setAction(){
        $string = (!isset($this->_explode[1]) || $this->_explode[1] == "inicial") ? "inicial" : $this->_explode[1];
        $string = $string ?: 'inicial';
        $this->_action = $string;
    }

    private function setParams(){
        unset($this->_explode[0], $this->_explode[1]);

        if(end($this->_explode) === null) array_pop($this->_explode);

        $ind = [];
        $value = [];
        $i = 0;

        if(!empty($this->_explode)){
            foreach ($this->_explode as $val) {
                if($i % 2 == 0){
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        }

        if(count($ind) === count($value) && !empty($ind)){
            $this->_params = array_combine($ind, $value);
        } else {
            $this->_params = [];
        }
    }
    
    protected function get($name){
        return $this->_params[$name] ?? '';
    }

    protected function post($name){
        $val = filter_input(INPUT_POST, $name, FILTER_SANITIZE_SPECIAL_CHARS);
        return $val ? trim($val) : '';
    }
    
    protected function irpara($endereco, $destino = '_self'){
        echo "<script> window.open('".$endereco."', target='$destino');</script>";
        exit();
    }

    protected function volta($n){
        echo "<script> history.go(-".$n."); </script>";
        exit();
    }

    protected function msg($msg){
        echo "<script> alert('".$msg."'); </script>";
    }

    public function erro(){
        $endereco = DOMINIO . $this->_url;
        $_SESSION['pagina_acionada'] = $endereco;
        $this->irpara(DOMINIO . 'erro');
        exit;
    }
    
    public function run(){
        $controllers_path = CONTROLLERS . 'controller_' . $this->_controller . '.php';
        
        if($this->_controller == 'sistema'){
            $this->irpara(DOMINIO . 'sistema/index.php');
            exit;
        }
        
        if(!file_exists($controllers_path)){
            $this->erro();
        } else {
            require_once($controllers_path);
            $app = new $this->_controller();
            $app->init();
            $action = $this->_action;
            if(!method_exists($app, $action)){
                $this->erro();
            } else {
                $app->$action();
            }
        }
    }
}
