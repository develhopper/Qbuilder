<?php
class QBuilder{
    /** @var \PDO */
    protected $db;
    protected $table;
    protected $query;
    protected $params=[];
    protected $fields=[];
    protected $foreign_keys=[];
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

    public function save($params=[]){
        $query="insert into $this->table (:C) values (:V)";
        $cols=[];
        $keys=[];
        if(!empty($params)){
            foreach($params as $key=>$param){
                $this->$key=$param;
            }
        }
        foreach($this->fields as $field){
            if(isset($this->$field)){
                array_push($cols, $field);
                array_push($keys,":$field");
                $this->params[":$field"]=$this->$field;
            }
        }
        $query=preg_replace("/:C/",implode(",",$cols),$query);
        $query = preg_replace("/:V/", implode(",",$keys), $query);
        $this->query=$query;
        if($this->execute())
            return $this->db->lastInsertId();
    }

    public function update($exec=false){
        $query="update $this->table set ";
        $cols=[];
        foreach($this->fields as $field){
            if(isset($this->$field)){
                array_push($cols,"$field=:$field");
                $this->params[":$field"]=$this->$field;
            }
        }
        $query.=implode(",",$cols);
        $this->query=$query;
        if($exec){
            $this->query.=" where id=$this->id";
            if($this->execute()){
                return true;
            }
        }
        return $this;
    }

    public function find($id){
        $this->query="select * from $this->table where id=$id";
        return $this->execute()->fetchObject(get_class($this));
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
        $this->params=[];
        return $stmt;
    }

    public function all(){
        $stmt=$this->select()->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,get_class($this));
    }

    public function hasOne($name){
        $model = new $name;
        $model->related = $this->table;
        $result=$model->select()->where($model->get_fkey(), $this->id)->first();
        // if(!$result)
        //     return $model;
        return $result;
    }

    public function hasMany($name){
        $model=new $name;
        $model->related=$this->table;
        return $model->select()->where($model->get_fkey(),$this->id)->get();
    }

    public function get_fkey(){
        if(isset($this->related)){
            return $this->foreign_keys[$this->related];
        }
    }

}
?>