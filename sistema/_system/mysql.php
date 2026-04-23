<?php
class mysql extends mysqli {
    public function __construct() {
        parent::__construct(SERVIDOR, USUARIO, SENHA, BANCO);
        if ($this->connect_errno) {
            die("Erro ao conectar ao banco de dados: " . $this->connect_error);
        }
        $this->set_charset("utf8");
    }

    public function __destruct() {
        if ($this->connect_errno === 0) {
            $this->close();
        }
    }

    // Executar query genérica (somente SELECT, DELETE sem params)
    public function executar($sql) {
        $result = $this->query($sql);
        if ($result === false) {
            throw new Exception("Erro na query: " . $this->error);
        }
        return $result;
    }

    // Inserir dados usando prepared statement
    public function inserir($tabela, array $dados) {
        $campos = implode(", ", array_keys($dados));
        $placeholders = implode(", ", array_fill(0, count($dados), "?"));
        $tipos = $this->getTipos(array_values($dados));

        $sql = "INSERT INTO `$tabela` ($campos) VALUES ($placeholders)";
        $stmt = $this->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query: " . $this->error);
        }

        $stmt->bind_param($tipos, ...array_values($dados));
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Erro na execução: " . $stmt->error);
        }

        $afected_rows = $stmt->affected_rows;
        $stmt->close();
        return $afected_rows;
    }

    // Alterar dados usando prepared statement
    public function alterar($tabela, array $dados, $condicoes, $condicoes_params = [], $condicoes_types = "") {
        $set_clauses = [];
        foreach ($dados as $campo => $valor) {
            $set_clauses[] = "`$campo` = ?";
        }
        $set_string = implode(", ", $set_clauses);

        $tipos = $this->getTipos(array_values($dados));
        $params = array_values($dados);

        // Junta os parâmetros dos dados + parâmetros da condição (se houver)
        if (!empty($condicoes_params)) {
            $tipos .= $condicoes_types;
            $params = array_merge($params, $condicoes_params);
        }

        $sql = "UPDATE `$tabela` SET $set_string WHERE $condicoes";
        $stmt = $this->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query: " . $this->error);
        }

        // Para passar os parâmetros para bind_param, precisamos passar variáveis por referência
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }

        array_unshift($refs, $tipos);
        call_user_func_array([$stmt, 'bind_param'], $refs);

        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Erro na execução: " . $stmt->error);
        }

        $afected_rows = $stmt->affected_rows;
        $stmt->close();
        return $afected_rows;
    }

    // Apagar dados usando prepared statement
    public function apagar($tabela, $condicoes, $condicoes_params = [], $condicoes_types = "") {
        $sql = "DELETE FROM `$tabela` WHERE $condicoes";
        $stmt = $this->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar query: " . $this->error);
        }

        if (!empty($condicoes_params)) {
            $refs = [];
            foreach ($condicoes_params as $key => $value) {
                $refs[$key] = &$condicoes_params[$key];
            }
            array_unshift($refs, $condicoes_types);
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }

        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Erro na execução: " . $stmt->error);
        }

        $afected_rows = $stmt->affected_rows;
        $stmt->close();
        return $afected_rows;
    }

    public function ultimo_id() {
        return $this->insert_id;
    }

    // Função para determinar os tipos para bind_param (i=integer, d=double, s=string, b=blob)
    private function getTipos($valores) {
        $tipos = "";
        foreach ($valores as $valor) {
            if (is_int($valor)) {
                $tipos .= "i";
            } elseif (is_float($valor)) {
                $tipos .= "d";
            } elseif (is_null($valor)) {
                // Para NULL usaremos string e depois passamos NULL explicitamente
                $tipos .= "s";
            } else {
                $tipos .= "s";
            }
        }
        return $tipos;
    }
}
