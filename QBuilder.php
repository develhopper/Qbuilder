<?php
class QBuilder{
    /** @var \PDO */
    protected $db;
    protected $table;
    protected $query;
    protected $params=[];
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

    public function where(...$cond){
        $condition=" where ";
        if(count($cond)==2){
            $this->params[":cond"]=$cond[1];
            $condition.="$cond[0]=:cond";
        }else{
            $this->params[":cond"] = $cond[2];
            $condition.="$cond[0]$cond[1]:cond";
        }
        $this->query.=$condition." ";
        return $this;
    }

    public function first(){
        $this->query.=" limit 1";
        return $this->execute()->fetchObject(get_class($this));
    }

    public function get(){
        return $this->execute()->fetchAll(PDO::FETCH_CLASS,get_class($this));
    }

    public function execute(){
        $stmt=$this->db->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt;
    }

    public function all(){
        $stmt=$this->select()->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,get_class($this));
    }

}
?>