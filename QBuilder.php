<?php
class QBuilder{
    /** @var \PDO */
    protected $db;
    protected $query;
    public function __construct()
    {
        $this->db=DB::getInstance();
    }

    public function execQuery($query){
        $stmt=$this->db->query($query);
        if($stmt){
            debug(get_class($this));
            return $stmt->fetchAll(PDO::FETCH_CLASS,get_class($this));
        }
    }

}
?>