<?php

/**
 * Created by creatorfromhell.
 * Date: 12/25/15
 * Time: 10:33 AM
 * Version: Beta 2
 */
class User
{
    public $id = null;
    /**
     * @var null
     */
    public $ip = null;
    /**
     * @var string
     */
    public $avatar = "";
    /**
     * @var null
     */
    public $name = null;
    /**
     * @var null
     */
    public $password = null;
    /**
     * @var null
     */
    public $group = null;
    /**
     * @var array
     */
    public $permissions = array();
    /**
     * @var null
     */
    public $email = null;
    /**
     * @var null
     */
    public $registered = null;
    /**
     * @var null
     */
    public $logged_in = null;
    /**
     * @var null
     */
    public $activation_key = null;
    /**
     * @var int
     */
    public $activated = 0;
    /**
     * @var int
     */
    public $banned = 0;
    /**
     * @var int
     */
    public $online = 0;


    public function __construct($instance = null) {

        $this->ip = WebCore::get_module("UtilityModule")->get_ip();
    }

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

        if($this->group instanceof Group) {
            return $this->group->has_permission($id);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function is_admin() {
        if($this->group instanceof Group) {
            return $this->group->is_admin();
        }
        return false;
    }

    /**
     *
     */
    public function save() {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_users";
        $perm = implode(",", $this->permissions);
        $stmt = $sql->get_pdo()->prepare("UPDATE `".$t."` SET user_name = ?, user_password = ?, user_email = ?, user_group = ?, user_permissions = ?, user_avatar = ?, user_ip = ?, user_registered = ?, logged_in = ?, user_banned = ?, user_online = ?, user_activated = ?, activation_key = ? WHERE id = ?");
        $stmt->execute(array($this->name, $this->password, $this->email, $this->group->id, $perm, $this->avatar, $this->ip, $this->registered, $this->logged_in, $this->banned, $this->online, $this->activated, $this->activation_key, $this->id));
    }

    /**
     *
     */
    public function send_activation() {
        global $url, $admin_email;
        $headers = 'From: '.$admin_email."\r\n" . 'Reply-To: '.$admin_email . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        mail($this->email, "Account Activation", "Hello ".$this->name.",\r\n You or someone using your email has registered on ".$url.". Please click the following link if you registered on this site, ".$url."/activation.php?page=activate&name=".$this->name."&key=".$this->activation_key.".", $headers);
    }

    /**
     * @param $name
     * @param bool $email
     * @param bool $id
     * @return User
     */
    public function load($name, $email = false, $id = false) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_users";
        $query = "SELECT id, user_password, user_email, user_group, user_permissions, user_avatar, user_ip, user_registered, logged_in, user_banned, user_online, user_activated, activation_key FROM `".$t."` WHERE user_name = ?";
        if($email) {
            $query = "SELECT id, user_password, user_name, user_group, user_permissions, user_avatar, user_ip, user_registered, logged_in, user_banned, user_online, user_activated, activation_key FROM `".$t."` WHERE user_email = ?";
        }
        if($id) {
            $query = "SELECT user_password, user_email, user_name, user_group, user_permissions, user_avatar, user_ip, user_registered, logged_in, user_banned, user_online, user_activated, activation_key FROM `".$t."` WHERE id = ?";
        }

        $user = new User();
        $stmt = $sql->get_pdo()->prepare($query);
        $stmt->execute(array($name));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $user->id = ($id) ? $name : $result['id'];
        $user->ip = $result['user_ip'];
        $user->avatar = $result['user_avatar'];
        $user->name = ($email) ? $result['user_name'] : ($id) ? $result['user_name'] : $name;
        $user->password = $result['user_password'];
        $user->group = Group::load($result['user_group']);
        $user->permissions = explode(",", $result['user_permissions']);
        $user->email = ($email) ? $name : $result['user_email'];
        $user->registered = $result['user_registered'];
        $user->logged_in = $result['logged_in'];
        $user->activation_key = $result['activation_key'];
        $user->activated = $result['user_activated'];
        $user->banned = $result['user_banned'];
        $user->online = $result['user_online'];
        return $user;
    }

    public static function get_name($id) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_users";
        return $sql->value($t, 'user_name', "WHERE id = ?", array($id));
    }

    /**
     * @param $name
     * @param bool $email
     * @return bool
     */
    public static function exists($name, $email = false) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $extra = ($email) ? "WHERE user_email = ?" : "WHERE user_name = ?";
        return $sql->has_values('users', $extra, array($name));
    }

    /**
     * @param $name
     * @param bool $email
     * @return mixed
     */
    public static function get_hashed_password($name, $email = false) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $extra = ($email) ? "WHERE user_email = ?" : "WHERE user_name = ?";
        return $sql->value('users', 'user_password', $extra, array($name));
    }

    /**
     * @param $user
     */
    public static function add_user($user) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        if(!is_a($user, "User")) { return; }
        $t = $sql->get_config("SQL", "sql_prefix")."_users";
        $perm = implode(",", $user->permissions);
        $stmt = $sql->get_pdo()->prepare("INSERT INTO `".$t."` (id, user_name, user_password, user_email, user_group, user_permissions, user_avatar, user_ip, user_registered, logged_in, user_banned, user_online, user_activated, activation_key) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($user->id, $user->name, $user->password, $user->email, $user->group->id, $perm, $user->avatar, $user->ip, $user->registered, $user->loggedIn, $user->banned, $user->online, $user->activated, $user->activationKey));
    }

    /**
     * @param $id
     */
    public static function delete($id) {
        $sql = self::sql();
        if(!($sql instanceof SQLModule)) { return; } //Should never happen

        $t = $sql->get_config("SQL", "sql_prefix")."_users";
        $stmt = $sql->get_pdo()->prepare("DELETE FROM `".$t."` WHERE id = ?");
        $stmt->execute(array($id));
    }
}