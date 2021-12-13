<?php
namespace QB\Migration;
use QB\Migration\Migration;

class Column{
    
    public $group_unique = Null;
    public $group_primary = Null;
    public $connection = Null;

    public $column_name = Null;
    public $table_name = Null;
    public $is_primary = false;
    public $str = Null;


    private function __construct($name){
        $this->column_name = $name;
    }

    public static function IntegerField($name, $options = array()){
        $instance = new Column($name);
        
        $instance->str = "$name INT";
        
        if(isset($options['increament']) && $options['increament'] == true)
            $instance->str .= " AUTO_INCREMENT ";
        
        $instance->str .= $instance->parse_options($options);

        return $instance;
    }

    public static function StringField($name, $size, $options = array()){
        $instance = new Column($name);
        
        $instance->str = "$name VARCHAR($size)";
        $instance->str .= $instance->parse_options($options);
        
        return $instance;
    }

    public static function TextField($name, $options = array()){
        $instance = new Column($name);

        $instance->str = "$name TEXT";
        $instance->str .= $instance->parse_options($options);

        return $instance;
    }

    public static function DateField($name, $current_timestamp = false){
        $instance = New Column($name);
        
        $instance->str = "$name DATE";

        if($current_timestamp){
            $instance->str .= " DEFAULT CURRENT_TIMESTAMP";
        }

        return $instance;
    }

    public static function TimeField($name, $current_timestamp = false){
        $instance = New Column($name);
        
        $instance->str = "$name TIME";

        if($current_timestamp){
            $instance->str .= " DEFAULT CURRENT_TIMESTAMP";
        }

        return $instance;
    }

    public static function DateTimeField($name, $current_timestamp = false){
        $instance = New Column($name);
        
        $instance->str = "$name DATETIME";

        if($current_timestamp){
            $instance->str .= " DEFAULT CURRENT_TIMESTAMP";
        }

        return $instance;
    }


    public function parse_options($options){
        $result = "";

        extract($options);
        
        if(isset($nullable) && $nullable == false){
            $result .= " NOT NULL";
        }

        if(isset($primary) && $primary === true){
            $result .= " PRIMARY KEY";
            $this->is_primary = true;
        }
        elseif(isset($primary) && is_string($primary)){
            $this->is_primary = true;
            $this->group_primary = $primary;
        }

        if(isset($unique) && $unique === true){
            $result .= "  UNIQUE ";
        }
        elseif(isset($unique) && is_string($unique)){
            $this->group_unique = $unique;
        }

        if(isset($default) && !empty($default)){
            $result .= " DEFAULT $default";
        }

        if(isset($connect)){
            if($connect instanceof Migration)
                $connect = $connect->get_pk();
            
            $options = array();
            if(isset($on_delete) && !empty($on_delete))
                $options['on_delete'] = $on_delete;
            if(isset($on_update) && !empty($on_update))
                $options['on_update'] = $on_update;

            $this->connect_table($connect,$options);
        }

        return $result;
    }

    public function connect_table($foreign, $options=array()){
        $this->connection = "FOREIGN KEY({$this->column_name}) REFERENCES {$foreign->table_name}({$foreign->column_name})";
        if(isset($options['on_delete']))
            $this->connection .= " ON DELETE ".strtoupper($options['on_delete']);
            
        if(isset($options['on_update']))
            $this->connection .= " ON UPDATE ".strtoupper($options['on_update']);
    }

    public function get_column_name(){
        return "`{$this->table_name}.{$this->column_name}`";
    }

}
