<?php

namespace QB\Migration;
use QB\DB;

class Migration{

    /** @var \PDO */
    protected $db;
    protected $table_name = Null;
    protected $statement = Null;
    protected $primary_keys = array();
    
    protected $primary_group = array();
    protected $unique_group = array();
    protected $connections = array();

    protected $parts = array();

    protected $columns = array();

    private function __construct($name){
        $this->db = DB::getInstance();
        $this->table_name = $name;
    }

    public static function create_table($name, ...$columns){
        $instance = new Migration($name);
        $instance->statement = "CREATE TABLE $name (";
        
        foreach($columns as $column){
            $instance->check_column($column);
            $column->table_name = $name;
            array_push($instance->parts, $column->str);
        }

        $instance->parts = array_merge($instance->parts, $instance->primaries(),
        $instance->uniques(), $instance->connections);

        $instance->statement .= implode(',' ,$instance->parts);

        $instance->statement .= ")";

        $instance->execute();

        return $instance;
    }

    public function check_column($column){
        if($column->is_primary)
            array_push($this->primary_keys,$column);
        
        if($column->group_primary != Null){
            if(!isset($this->primary_group[$column->group_primary]))
                $this->primary_group[$column->group_primary] = array();
            
            array_push($this->primary_group[$column->group_primary],$column->column_name);
        }
        
        if($column->group_unique != Null){
            if(!isset($this->primary_group[$column->group_unique]))
                $this->primary_group[$column->group_unique] = array();
            
            array_push($this->unique_group[$column->group_unique],$column->column_name);
        }

        if($column->connection != Null){
            array_push($this->connections, $column->connection);
        }

        $this->columns[$column->column_name] = $column;
    }

    private function primaries(){
        $result = array();
        foreach($this->primary_group as $group){
            array_push($result,"PRIMARY KEY (". implode(',',$group) . ")");
        }
        return $result;
    }

    private function uniques(){
        $result = array();
        foreach($this->unique_group as $key => $group){
            array_push($result,"UNIQUE KEY `unique_$key` (". implode(',',$group) . ")");
        }
        return $result;
    }

    public function __get($key){
        if(isset($this->columns[$key]))
            return $this->columns[$key];
        return $this->$key;
    }

    public function get_pk(){
        return $this->primary_keys[0];
    }

    private function execute(){
        $this->db->query($this->statement);
    }
}

