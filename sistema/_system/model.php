<?php
class model {

    protected $db;

    public function __construct() {
        // Cria conexão com banco, reutilizando sua classe mysql atualizada
        $this->db = new mysql();
    }

    // Método para buscar dados, recebe SQL e parâmetros opcionais para prepared statements
    public function fetch($sql, $types = null, $params = null) {
        $result = $this->db->executar($sql, $types, $params);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Método para buscar um único registro
    public function fetchOne($sql, $types = null, $params = null) {
        $result = $this->db->executar($sql, $types, $params);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Inserir um registro (chama método inserir da classe mysql)
    public function insert($table, array $data) {
        return $this->db->inserir($table, $data);
    }

    // Atualizar um registro
    public function update($table, array $data, $condition, $condTypes = '', $condParams = []) {
        return $this->db->alterar($table, $data, $condition, $condTypes, $condParams);
    }

    // Apagar um registro
    public function delete($table, $condition, $condTypes = '', $condParams = []) {
        return $this->db->apagar($table, $condition, $condTypes, $condParams);
    }
}
