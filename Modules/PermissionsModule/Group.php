<?php

/**
 * Created by creatorfromhell.
 * Date: 12/25/15
 * Time: 10:33 AM
 * Version: Alpha 1
 */
class Group
{
    /**
     * @var null
     */
    public $id = null;
    /**
     * @var null
     */
    public $name = null;
    /**
     * @var null
     */
    public $admin = null;
    /**
     * @var array
     */
    public $permissions = array();
    /**
     * @var null
     */
    public $preset = null;

    public static function instance() {
        return WebCore::get_module("PermissionsModule");
    }

    public static function sql() {
        return self::instance()->get_sql();
    }

    /**
     * @param $id
     * @return bool
     */
    public function has_permission($id) {
        foreach($this->permissions as &$perm) {
            if($perm == $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function is_admin() {
        return ($this->admin == 1) ? true : false;
    }

    /**
     *
     */
    public function save() {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $perm = implode(",", $this->permissions);
        $t = $sql->get_config("SQL", "sql_prefix")."_groups";
        $stmt = $sql->get_pdo()->prepare("UPDATE `".$t."` SET group_name = ?, group_permissions = ?, group_admin = ?, group_preset = ? WHERE id = ?");
        $stmt->execute(array($this->name, $perm, $this->admin, $this->preset, $this->id));
    }

    /**
     * @param $group
     */
    public static function add_group($group) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        if(!($group instanceof Group)) { return; }

        $t = $sql->get_config("SQL", "sql_prefix")."_groups";
        $perm = implode(",", $group->permissions);
        $stmt = $sql->get_pdo()->prepare("INSERT INTO `".$t."` (id, group_name, group_permissions, group_admin, group_preset) VALUES(?, ?, ?, ?, ?)");
        $stmt->execute(array($group->id, $group->name, $perm, $group->admin, $group->preset));
    }

    /**
     * @param $id
     * @return Group
     */
    public static function load($id) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_groups";
        $group = new Group();
        $stmt = $sql->get_pdo()->prepare("SELECT group_name, group_permissions, group_admin, group_preset FROM `".$t."` WHERE id = ?");
        $stmt->execute(array($id));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $group->id = $id;
        $group->name = $result['group_name'];
        $group->permissions = explode(",", $result['group_permissions']);
        $group->admin = $result['group_admin'];
        $group->preset = $result['group_preset'];
        return $group;
    }

    /**
     * @return mixed
     */
    public static function preset() {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        return $sql->value("groups", "id", " WHERE group_preset = 1");
    }

    /**
     * @param $id
     */
    public static function delete($id) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_groups";
        $stmt = $sql->get_pdo()->prepare("DELETE FROM `".$t."` WHERE id = ?");
        $stmt->execute(array($id));
    }
}