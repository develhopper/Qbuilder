<?php
class QBuilder{
    /** @var \PDO */
    protected $db;
    protected $table;
    protected $query;
    
    public function __construct()
    {
        $this->db=DB::getInstance();
    }

    public function execQuery($query){
        return $this->db->query($query);
    }

    public function select($cols="*"){
        $this->query="select $cols from $this->table";
        return $this;
    }

    public function execute(){
        $stmt=$this->db->prepare($this->query);
        $stmt->execute();
        return $stmt;
    }

    public static function all(){
        $builder=new QBuilder();
        $builder->table="users";
        $stmt=$builder->select()->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,get_called_class());
    }

}
?>