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
    protected $related;
    protected $related_tables=[];
    protected $pivot_table=[];
    protected $changed=[];
    protected $query;
    protected $alias;

    public function __construct()
    {
        $this->db=DB::getInstance();
    }

    public function __set($key,$value){
        if(property_exists($this,$key))
            $this->$key=$value;
        else{
            if(in_array($key,array_keys($this->fields)))
                array_push($this->changed,$key);
            $this->fields[$key]=$value;
        }
    }

    public function __get($key){
        if(property_exists($this,$key))
            return $this->$key;
        else
            return $this->fields[$key];
    }

    public function execQuery($query){
        return $this->db->query($query);
    }

    public function select($cols="*"){
        $this->query="select $cols from $this->table";
        if(isset($this->alias)){
            $this->query.=" $this->alias";
        }
        return $this;
    }

    public function where(...$cond){
        $condition=" where ";
        if(count($cond)==2){
            $this->params[":cond"]=$cond[1];
            $condition.=" $cond[0]=:cond ";
        }else{
            $this->params[":cond"] = $cond[2];
            $condition.=" $cond[0] $cond[1] :cond ";
        }
        $this->query.=$condition." ";
        return $this;
    }

    public function and(...$cond){
        $condition=" and ";
        if(count($cond)==2){
            $this->params[":and"]=$cond[1];
            $condition.=" $cond[0]=:and ";
        }else{
            $this->params[":and"] = $cond[2];
            $condition.=" $cond[0] $cond[1] :and ";
        }
        $this->query.=$condition." ";
        return $this;
    }

    public function or(...$cond){
        $condition=" or ";
        if(count($cond)==2){
            $this->params[":or"]=$cond[1];
            $condition.=" $cond[0]=:or ";
        }else{
            $this->params[":or"] = $cond[2];
            $condition.=" $cond[0]$cond[1]:or ";
        }
        $this->query.=$condition." ";
        return $this;
    }

    public function sort($by,$mode="ASC"){
        $this->query.=" ORDER By $by $mode";
        return $this;
    }

    public function upsert($values=[]){
        $query="insert into $this->table (:C) values (:V) on duplicate key update (:U)";
        $cols=[];
        $keys=[];
        foreach($this->fields as $key=>$value){
            array_push($cols, $key);
            array_push($keys,":$key");
            $this->params[":$key"]=$this->fields[$key];
        }
        $query=preg_replace("/:C/",implode(",",$cols),$query);
        $query = preg_replace("/:V/", implode(",",$keys), $query);
        $u="";
        for($i=0;$i<count($cols);$i++){
            $u.="$cols[$i]=$keys[$i],";
        }
        foreach($values as $key=>$value){
            $u=str_replace($key,$value,$u);
        }
        $u=rtrim($u,",");
        $this->query=str_replace("(:U)",$u,$query);
        return $this->execute();
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

        foreach($this->fields as $key=>$value){
                array_push($cols, $key);
                array_push($keys,":$key");
                $this->params[":$key"]=$this->fields[$key];
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
        $query="update $this->table set ";
        $cols=[];

        foreach($this->changed as $key){
            array_push($cols,"$key=:$key");
            $this->params[":$key"]=$this->fields[$key];
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

    public function first($count=1){
		$this->query.=" limit $count";
		if($count==1)
			return $this->execute()->fetchObject(get_class($this));
		else
			return $this->get();
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

    public function hasMany($name,$exec=true){
        $model=new $name;
        $model->related=$this->table;
        $primary_key=$this->primary;
        $model=$model->select()->where($model->get_fkey(),$this->$primary_key);
        if($exec)
            return $model->get();
        else
            return $model;
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

    public function join($name,$type="",$options=[]){
        $a="";$b="";
        if(!empty($options['aliases']) && count($options['aliases'])>1){
            $a=$options['aliases'][0];
            $b=$options['aliases'][1];
        }
        $model=new $name;
        $model->related=$this->table;

        $this->query.=" $type join {$model->table} $b ";

        if(isset($options["on"])){
            $this->query.=" on $options[on]";
            return $this;
        }

        if(empty($a))
            $a=$this->table;
        if(empty($b))
            $b=$model->table;
        if(isset($options['reverse_cond'])){
            $this->related=$model->table;
            $this->query.="on $a.{$this->get_fkey()}=$b.{$model->primary}";
        }
        else
            $this->query.="on $a.{$this->primary}=$b.{$model->get_fkey()}";
        return $this;
    }

    public function left_join($name,$options=[]){
        return $this->join($name,"left",$options);
    }

    public function right_join($name,$options=[]){
        return $this->join($name,"right",$options);
    }

    public function get_fkey(){
        if(isset($this->related)){
            return $this->related_tables[$this->related];
        }
    }

}
?>
