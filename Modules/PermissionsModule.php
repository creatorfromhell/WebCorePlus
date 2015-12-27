<?php

/**
 * Created by creatorfromhell.
 * Date: 12/25/15
 * Time: 10:26 AM
 * Version: Beta 2
 */
class PermissionsModule extends Module
{
    public $sql;

    public function __construct()
    {
        $this->set_directory('PermissionsModule');
        $this->set_name("PermissionsModule");

        $this->set_depends(array(
            "SQLModule",
            "UtilityModule"
        ));

        $this->set_configurations(array(
            "Main" => array(
                "default_group" => "Guest"
            )
        ));
    }

    public function init_module()
    {
        $this->sql = WebCore::get_module("SQLModule");
        if(!($this->sql instanceof SQLModule)) {
            throw new Exception("PermissionsModule: Unable to load SQLModule.");
        }
    }

    public function get_sql() {
        if(!($this->sql instanceof SQLModule)) {
            throw new Exception("PermissionsModule: Unable to load SQLModule.");
        }
        return $this->sql;
    }



    /*
     * Page Functions
     */
    /**
     * @param $user
     * @param string $node
     * @param bool $guest
     * @param bool $admin
     * @param string $group
     * @param bool $useGroup
     * @param string $name
     * @param bool $useName
     * @return bool
     */
    public function page_locked($user, $node = "", $guest = false, $admin = false, $group = "", $useGroup = false, $name = "", $useName = false) {
        if($useGroup) { return $this->page_locked_group($user, $group); }
        if($useName) { return $this->page_locked_user($user, $name); }
        if($admin) { return $this->page_locked_admin($user); }
        return $this->page_locked_node($user, $node, $guest);
    }

    /**
     * @param string $node
     */
    public function page_locked_node($user, $node, $guest = false) {
        if($guest) { return false; }
        if($user === null) { return true; }
        if(!($user instanceof User)) { return true; }
        if($user->is_admin()) { return false; }
        if(!$this->get_sql()->has_values("nodes", " WHERE node_name = ?", array($node))) { return true; }
        if($user->has_permission($this->node_id($node))) { return false; }
        if(!($user->group instanceof Group)) { return true; }
        if($user->group->has_permission($this->node_id($node))) { return false; }
        return true;
    }

    /**
     * @param $user
     * @return bool
     */
    public function page_locked_admin($user) {
        if($user === null) { return true; }
        if(!($user instanceof User)) { return true; }
        if($user->is_admin()) { return false; }
        return true;
    }

    /**
     * @param string $group
     */
    public function page_locked_group($user, $group) {
        if($user === null) { return true; }
        if(!($user instanceof User)) { return true; }
        if($user->is_admin()) { return false; }
        if(!($user->group instanceof Group)) { return true; }
        if($user->group->id == $group) { return false; }
        return true;
    }

    /**
     * @param string $name
     */
    public function page_locked_user($user, $name) {
        if($user === null) { return true; }
        if(!($user instanceof User)) { return true; }
        if($user->is_admin()) { return false; }
        if($user->name == $name) { return false; }
        return true;
    }


    /*
     * Group Functions
     */

    /*
     * User Functions
     */
    public function logged_in() {
        return WebCore::get_module("UtilityModule")->check_session("usersplusprofile");
    }

    /*
     * Node Functions
     */
    public function node_id($node) {
        return $this->get_sql()->value("nodes", "id", " WHERE node_name = ?", array($node));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function node_name($id) {
        return $this->get_sql()->value("nodes", "node_name", " WHERE id = ?", array($id));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function node_details($id) {
        $t = $this->get_sql()->get_config("SQL", "sql_prefix")."_nodes";
        $stmt = $this->get_sql()->get_pdo()->prepare("SELECT node_name, node_description FROM `".$t."` WHERE id = ?");
        $stmt->execute(array($id));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param $node
     * @param $description
     */
    public function node_add($node, $description) {
        $t = $this->get_sql()->get_config("SQL", "sql_prefix")."_nodes";
        $stmt = $this->get_sql()->get_pdo()->prepare("INSERT INTO `".$t."` (id, node_name, node_description) VALUES('', ?, ?)");
        $stmt->execute(array($node, $description));
    }

    /**
     * @param $id
     * @param $node
     * @param $description
     */
    public function node_edit($id, $node, $description) {
        $t = $this->get_sql()->get_config("SQL", "sql_prefix")."_nodes";
        $stmt = $this->get_sql()->get_pdo()->prepare("UPDATE `".$t."` SET node_name = ?, node_description = ? WHERE id = ?");
        $stmt->execute(array($node, $description, $id));
    }

    /**
     * @param $id
     */
    public function node_delete($id) {
        $t = $this->get_sql()->get_config("SQL", "sql_prefix")."_nodes";
        $stmt = $this->get_sql()->get_pdo()->prepare("DELETE FROM `".$t."` WHERE id = ?");
        $stmt->execute(array($id));
    }

    /*
     * Hashing/Generation Functions
     */
    /**
     * @param int $length
     * @return string
     */
    public function generate_salt($length = 25) {
        return substr(md5(WebCore::get_module("UtilityModule")->generate_uuid()), 0, $length);
    }

    /**
     * @param $value
     * @param bool $useSalt
     * @param string $salt
     * @return string
     */
    public function generate_hash($value, $useSalt = false, $salt = "") {
        if($useSalt) {
            if(trim($salt) != "" && strlen(trim($salt)) == 25) {
                return hash('sha256', $salt.$value);
            }
        }
        return hash('sha256', $value);
    }

    /**
     * @param $hash
     * @param $value
     * @return bool
     */
    public function check_hash($hash, $value) {
        return $hash === hash('sha256', $value);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generate_session_id($length = 35) {
        return substr(md5($this->generate_salt(30).WebCore::get_module("UtilityModule")->generate_uuid()), 0, $length);
    }
}