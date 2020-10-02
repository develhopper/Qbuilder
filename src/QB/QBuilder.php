<?php
namespace QB;

use PDO;
use QB\DB;

class QBuilder{
    /** @var \PDO */
    protected $db;
    protected $table;
    protected $primary="id";
    protected $params=[];
    protected $fields=[];
    protected $related_tables=[];
    protected $pivot_table=[];
    
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
        if(empty($this->fields)){
            $this->fields=array_keys($params);
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
        else
            return -1;
    }

    public function update($exec=false){
        //Bug:: this will update all cols

        $query="update $this->table set ";
        $cols=[];
        if(empty($this->fields))
            $this->fields=$this->public_fields();
        foreach($this->fields as $field){
            if(isset($this->$field)){
                array_push($cols,"$field=:$field");
                $this->params[":$field"]=$this->$field;
            }
        }
        $query.=implode(",",$cols);
        $this->query=$query;
        if($exec){
            $primary_key=$this->primary;
            $this->query.=" where $primary_key='{$this->$primary_key}'";
            return $this->execute()->rowCount();
        }
        return $this;
    }

    public function delete(...$cond){
        if(count($cond)>1){
            $this->query="delete from $this->table ";
            return $this->where(...$cond);
        }
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

    public function belongsTo($name){
        $model = new $name;
        return $model->select()->where($model->primary,$this->{$this->get_fkey()})->first();
    }

    public function belongsToMany($name){
        $model = new $name;
        $this->related = explode(":",$this->pivot_table[$model->table]);
        $this->query="select * from $this->table 
        join {$this->related[0]} on $this->table.$this->primary={$this->related[1]} 
        join $model->table on $model->table.id={$this->related[2]}";
        if(isset($this->id)){
            $this->query.=" where $this->table.id=$this->id";
        }
        return $this->get();
        // return $model->select()->where($model->primary, $this->{$this->get_fkey()})->get();
    }

    public function get_fkey(){
        if(isset($this->related)){
            return $this->related_tables[$this->related];
        }
    }

    public function public_fields(){
        $a=array_keys(get_class_vars(get_class($this)));
        $b=array_keys(get_object_vars($this));

        return array_diff($b,$a);
    }

}
?>