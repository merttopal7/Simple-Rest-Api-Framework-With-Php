<?php
Class xmlcontroller {

    public static function getXML($req) {
            $data = new query('products');
            $data->select('*');
            $data->where('list_id','=',$req->params->category);
            $data->limit(12,(($req->params->page ?? 1)-1)*10);
            return $data->all();
    }
    public static function getXMLists($req) {
            $data = new query('xml_lists');
            $data->select('*');
            return $data->all();
    }
     public static function getProduct($req) {
            $data = new query('products');
            $data->select('*');
            $data->where('id','=',$req->params->id);
            return $data->first();
    }
    
    
    public static function createXML($req) {
        if($req->params->uid == "kadin") {
            $list_id = "1";
            $xmlImport = new XmlImport();
            $xmlImport->xmlDataSource   =   "https://www.kadingiyimxml.com/index.php?do=catalog/output&pCode=6614708942";
            $xmlImport->mainTag         =   "root";
            $xmlImport->secondaryTag    =   "item";
            $xmlImport->model           =   "stockCode";
            $xmlImport->name            =   "label";
            $xmlImport->description     =   "details";
            $xmlImport->price           =   "price1";
            $xmlImport->qty             =   "stockAmount";
            $xmlImport->category        =   "category";
            $xmlImport->images          =   "picture:<>:Path";
            $datas = $xmlImport->LoadXML();
        }
        if($req->params->uid == "elektronik") {
            $list_id = "2";
            $xmlImport = new XmlImport();
            $xmlImport->xmlDataSource    =   "https://teknotok.com/wp-content/uploads/wpallexport/exports/747d523a551aacf463e7456f13184e57/current-Urunler-Export-2020-August-20-1437.xml?wpae_nocache=828064120";
            $xmlImport->mainTag          =   "data";
            $xmlImport->secondaryTag     =   "post";
            $xmlImport->model            =   "ID";
            $xmlImport->name             =   "Title";
            $xmlImport->description      =   "açıklama";
            $xmlImport->price            =   "Price";
            $xmlImport->qty              =   "Stock";
            $xmlImport->category         =   "Ürünkategorileri";
            $xmlImport->images           =   "Image";
            $datas = $xmlImport->LoadXML();
        }
        if(isset($datas)) {
            foreach($datas as $data) {
                    $insert = new query('products');
                    $insert->insert([
                    'list_id' => $list_id,
                    'model' => $data->model,
                    'name' => $data->name,
                    'description' => $data->description,
                    'price' => $data->price,
                    'qty' => $data->qty,
                    'category' => $data->category,
                    'images' => json_encode($data->images),
                    ]);
                    $insert->save();
            }
            return 'İşlem Tamamlandı !';
        }
    }

}