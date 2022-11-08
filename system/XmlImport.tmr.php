<?php
Class XmlImport {
    public $xmlDataSource;
    public $mainTag;
    public $secondaryTag;
    public $model;
    public $name;
    public $description;
    public $qty;
    public $price;
    public $category;
    public $images;

    public function xml2js($xmlnode) {
        $root = (func_num_args() > 1 ? false : true);
        $jsnode = array();
    
        if (!$root) {
            if (count($xmlnode->attributes()) > 0){
                $jsnode["$"] = array();
                foreach($xmlnode->attributes() as $key => $value)
                    $jsnode["$"][$key] = (string)$value;
            }
    
            $textcontent = trim((string)$xmlnode);
            if (strlen($textcontent) > 0)
                $jsnode["_"] = $textcontent;
    
            foreach ($xmlnode->children() as $childxmlnode) {
                $childname = $childxmlnode->getName();
                if (!array_key_exists($childname, $jsnode))
                    $jsnode[$childname] = array();
                array_push($jsnode[$childname], $this->xml2js($childxmlnode, true));
            }
            return $jsnode;
        } else {
            $nodename = $xmlnode->getName();
            $jsnode[$nodename] = array();
            array_push($jsnode[$nodename], $this->xml2js($xmlnode, true));
            return json_encode($jsnode);
        }
    }

    public function e($val) {
        return $val[0]->_ ?? null;
    }


    public function LoadXML() {
        $xml = simplexml_load_file($this->xmlDataSource);
        $data = json_decode($this->xml2js($xml))->{$this->mainTag}[0]->{$this->secondaryTag};
        $products = array();
        
        foreach($data as $item) {
            $product = new stdClass();
            $product->model = $this->e($item->{$this->model});
            $product->name = $this->e($item->{$this->name});
            $product->description = $this->e($item->{$this->description});
            $product->price = $this->e($item->{$this->price});
            $product->qty = $this->e($item->{$this->qty});
            $product->category = $this->e($item->{$this->category});
            $product->images = array();
            for($i=1;$i<9;$i++) {
                if(isset(explode(':<>:', $this->images)[1])) {
                    $pImage =  explode(':<>:', $this->images)[0].$i.explode(':<>:', $this->images)[1];
                    $ready = $this->e($item->{$pImage});
                } else {
                    $pImage =  $this->images;
                    $ready = $this->e($item->{$pImage},$i);
                }
                if(!empty(trim($ready))) {
                    array_push($product->images, $ready);
                }
            }
            array_push($products,$product);
        }
        return $products;
    }
}

?>