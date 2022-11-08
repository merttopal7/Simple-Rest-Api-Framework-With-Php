<?php
Class query {
    protected  $db;
    protected  $host = "localhost";
    protected  $username = "root";
    protected  $password = "";
    protected  $dbname = "xmlimport";
    protected  $tablename = "";
    protected  $query = "";
    protected  $where = false;

    public  function __construct($queryFor = false) {
        try {$this->db = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname.";charset=utf8", $this->username, $this->password);
        } catch ( PDOException $e ){print $e->getMessage();}  
        if($queryFor) $this->tablename = $queryFor;
    }

    public  function table($table) {
        $this->tablename = $table;
    }

    public  function select($select) {
        $this->query = "Select $select From ".$this->tablename;
    }
    public  function insert(array $insert) {
        $this->query = "INSERT INTO ".$this->tablename;
        foreach($insert as $key => $item){
            $pre='"';$item = join('\"', explode('"', $item));
            $keys[]=$key;
            $values[]=$pre.$item.$pre;
        }
        $keysString = "(".join(',', $keys).")";
        $valuesString = "(".join(',', $values).")";
        $this->query .= " ".$keysString." VALUES".$valuesString;
        return $this->query;
    }
    public  function update(array $update) {
        $this->query = "UPDATE ".$this->tablename." SET ";
        $i=0;
        $comma = ",";
        foreach($update as $key => $item){
            $pre='"';$item = join('\"', explode('"', $item));
            if(++$i === count($update)) $comma = "";
            $this->query .= " $key = $pre$item$pre".$comma; 
        }
        return $this->query;
    }
    public  function limit($num,$offset = 0) {
        $this->query .= " LIMIT ".$num." OFFSET ".$offset;
    }
    public  function delete() {
        if($this->where) {
            $this->query = "DELETE FROM ".$this->tablename.$this->query;
            $this->save();
            return $this->query;
        } else {
            return "Delete method cannot be used without where method !";
        }
    }

    public  function where($where,$operator,$value) {
        $this->where = true;
        $point=strpos($value, '"')!==false ? "'":'"';
        $pre = "Where";
        $this->query .= " ".$pre." "."$where $operator $point$value$point";
        return $this->query;
    }
    public  function orWhere($where,$operator,$value) {
        $point=strpos($value, '"')!==false ? "'":'"';
        $pre = "or";
        $this->query .= " ".$pre." "."$where $operator $point$value$point";
    }
    public  function andWhere($where,$operator,$value) {
        $point=strpos($value, '"')!==false ? "'":'"';
        $pre = "and";
        $this->query .= " ".$pre." "."$where $operator $point$value$point";
    }
    public  function all() {
        return ($this->db)->query($this->query)->fetchAll(PDO::FETCH_OBJ);
    }
    public  function first() {
        return ($this->db)->query($this->query)->fetch(PDO::FETCH_OBJ);
    }

    public function save() {
        return ($this->db)->query($this->query);
    }

}