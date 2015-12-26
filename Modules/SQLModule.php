<?php

/**
 * Created by creatorfromhell.
 * Date: 12/19/15
 * Time: 9:30 AM
 * Version: Beta 2
 */
class SQLModule extends Module
{
    private $pdo;

    public function __construct($auto_connect = false)
    {
        $this->set_directory('SQLModule');
        $this->set_name("SQLModule");


        $this->set_configurations(array(
            "SQL" => array(
                "sql_host" => "localhost",
                "sql_db" => "database",
                "sql_user" => "user",
                "sql_password" => "password"
            )
        ));

        if($auto_connect) {
            $this->connect();
        }
    }

    public function connect() {
        $host = parent::get_config("SQL", "sql_host");
        $db = parent::get_config("SQL", "sql_db");
        $user = parent::get_config("SQL", "sql_user");
        $password = parent::get_config("SQL", "sql_password");

        $this->pdo = new PDO("mysql:host=".$host.";dbname=".$db, $user, $password);
    }

    public function init_module()
    {
        require_once(parent::get_directory()."SQLReader.php");
    }

    public function get_pdo() {
        if(!($this->pdo instanceof PDO)) { return null; }
        return $this->pdo;
    }

    public function query_file($file) {
        $reader = new SQLReader($file);
        $queries = $reader->parse_queries();
        foreach($queries as &$query) {
            $this->query($query);
        }
    }

    public function query($query, $parameters = array()) {
        if(!($this->pdo instanceof PDO)) { return null; }
        $statement = $this->pdo->prepare($query);
        return $statement->execute($parameters);
    }

    public function value($table, $column, $extra = '', $params = array()) {
        if(!($this->pdo instanceof PDO)) { return null; }
        $statement = $this->pdo->prepare("SELECT ".$column." FROM `".$table."`".$extra);
        $statement->execute($params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result[$column];
    }

    public function values($table, $column, $extra = '', $params = array()) {
        if(!($this->pdo instanceof PDO)) { return null; }
        $statement = $this->pdo->prepare("SELECT ".$column." FROM `".$table."`".$extra);
        $statement->execute($params);
        $result = $statement->fetchAll(PDO::FETCH_COLUMN);
        $values = array();
        foreach($result as &$r) {
            $values[] = $r;
        }
        return $values;
    }

    public function has_values($table, $extra = '', $params = array()) {
        if($this->count_columns($table, $extra, $params) > 0) {
            return true;
        }
        return false;
    }

    public function count_columns($table, $extra = '', $params = array()) {
        if(!($this->pdo instanceof PDO)) { return null; }
        $statement = $this->pdo->prepare("SELECT * FROM `".$table."`".$extra);
        $statement->execute($params);
        return $statement->fetch(PDO::FETCH_NUM);
    }
}