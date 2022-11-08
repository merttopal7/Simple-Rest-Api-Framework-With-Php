<?php
Class controller {

    //middleware
    public static function middleware($req) {
        $data['Request'] = $req->params->slug;
        $data['Middleware'] = "This value setted via middleware";
        app::next($data);
    }

    public static function index($req) {
        $data = new query('xml_lists');
        return $data->delete();
    }
    
    public static function s($req) {
        $data = new query('xml_lists');
        $data->select('*');
        $data->where('id','=','1');
        $data->orWhere('id','=','2');
        return $data->all();
    }
}