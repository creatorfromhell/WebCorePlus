<?php

/**
 * Created by creatorfromhell.
 * Date: 12/20/15
 * Time: 2:48 PM
 * Version: Beta 2
 */
class PaginateModule extends Module
{

    /**
     * @var string
     */
    public $return_value = "?";

    /**
     * @var string
     */
    public $extra_query = "";

    /**
     * @var int
     */
    public $starting_value = 0;

    /**
     * @var int
     */
    public $items = 10;

    /**
     * @var float|int
     */
    public $page = 1;

    /**
     * @var float
     */
    public $total_pages = 1.0;

    /**
     * @var string
     */
    public $column_string = "*";

    /**
     * @var string
     */
    public $table = "";

    /**
     * @var string
     */
    public $page_string = "";

    private $sql;

    public function __construct($t, $c, $p, $i = 10, $r = "?", $extra = "")
    {
        $this->set_directory('PaginateModule');
        $this->set_name("PaginateModule");
        $this->set_depends("SQLModule");


        $this->table = $t;
        $this->column_string = $c;
        $this->page = $p;
        $this->items = $i;
        $this->return_value = $r;
        $this->extra_query = $extra;
        $this->prepare_values();
        if($this->page > $this->total_pages) {
            $this->page = $this->total_pages;
            $this->prepare_values();
        }
        $this->build_page_string();
    }

    public function init_module()
    {
        $this->sql = WebCore::get_module("SQLModule");
    }

    /**
     *
     */
    public function paginate() {
        if(!($this->sql instanceof SQLModule)) { return; }

        $stmt = $this->sql->get_pdo()->prepare("SELECT ".$this->column_string." FROM `".$this->table."` ".$this->extra_query." LIMIT ".$this->starting_value.", ".$this->items);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out = "";
            $out .= "<tr>";
            $columns = explode(", ", $this->column_string);
            foreach($columns as &$column) {
                $out .= "<td>".$row[$column]."</td>";
            }
            $out .= "</tr>";
            echo $out;
        }
    }

    /**
     * @return array
     */
    public function paginate_return() {
        if(!($this->sql instanceof SQLModule)) { return null; }

        $stmt = $this->sql->get_pdo()->prepare("SELECT ".$this->column_string." FROM `".$this->table."` ".$this->extra_query." LIMIT ".$this->starting_value.", ".$this->items);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     *
     */
    public function prepare_values() {
        if(!($this->sql instanceof SQLModule)) { return; }

        $stmt1 = $this->sql->get_pdo()->prepare("SELECT Count(*) FROM `".$this->table."`");
        $stmt1->execute();
        $count = intval($stmt1->fetchColumn());

        $this->starting_value = (($this->page - 1) * $this->items);
        $this->total_pages = ceil($count / $this->items);
    }

    /**
     *
     */
    public function build_page_string() {
        $this->page_string = "";
        $this->page_string .= "<div id='pages'>";
        $this->page_string .= "<strong><a href='".$this->return_value."pn=1'>First</a></strong>";
        for($i = 1; $i <= $this->total_pages; $i++) {
            $active = ($i == $this->page) ? "class='active'" : "";
            $this->page_string .= "<a ".$active." href='".$this->return_value."pn=".$i."'>".$i."</a>";
        }
        $this->page_string .= "<strong><a href='".$this->return_value."pn=".$this->total_pages."'>Last</a></strong>";
        $this->page_string .= "</div>";
    }
}