<?php
Class app {
    static $currentRoute;
    static $currentMethod;
    static $middleware;
    static $basePath = "/";
    static $validUrl = false;
    public static function init(){self::$currentRoute=strtolower(php_sapi_name()=='cli-server'?strtok(substr($_SERVER["REQUEST_URI"],1,strlen($_SERVER["REQUEST_URI"])), '?'):$_GET['route']);self::$currentMethod = $_SERVER['REQUEST_METHOD'];}
    public static function validUrl(){if(!self::$validUrl){http_response_code(404);die('Cannot Found '.self::$currentMethod.' /'.self::$currentRoute);}}
    public static function base($data){self::$basePath=(isset($data[0]) && $data[0] == "/" ? substr($data,1,strlen($data)) : $data);}
    public static function next($data=true){self::$validUrl=false;self::$middleware=$data;}
    public static function initUrl($url) {$explodedUrl=explode('/',strtolower($url));$explodedRoute=explode('/',self::$currentRoute);
    $params = new stdClass();
        for($i = 0; $i < count($explodedUrl) ; $i++) {
            if($explodedUrl[$i]) $explodedSingle=explode(':',$explodedUrl[$i]);
            if(isset($explodedSingle[1]) && isset($explodedRoute[$i])) $params->{$explodedSingle[1]}=$explodedRoute[$i];
            if(isset($explodedSingle[1])) $explodedUrl[$i]=$params->{$explodedSingle[1]};
        }
    $response=new stdClass();
    $response->url=join('/',$explodedUrl);
    $response->params=$params;
    return $response;
    }
    public function __construct(){self::init();}

    public function __destruct(){self::validUrl();}

    public static function go($route,$callBack = false,$callBack2 = false,$method = "GET") {
    if(isset(self::$basePath[0]) && self::$basePath[0] == "/") self::$basePath = substr(self::$basePath,1,strlen(self::$basePath));
    if(strlen(self::$basePath) - 1 > -1 && self::$basePath[strlen(self::$basePath)-1] != "/") self::$basePath .= "/";
    if($route[0] == "/") $route = substr($route,1,strlen($route));
    $route = self::$basePath.$route;
    if(self::$currentMethod == $method && !self::$validUrl &&  count(explode('/',$route)) == count(explode('/',self::$currentRoute))) {
        $initUrl=self::initUrl($route);$req=new stdClass();$req->route=new stdClass();$req->route->fullUrl=$initUrl->url;$req->route->method=self::$currentMethod;$req->params=$initUrl->params;
        if(self::$currentRoute == $req->route->fullUrl){
            $req->params = (object)array_merge((array)$req->params, (array)$_REQUEST);
                if($callBack){
                    $mainData = $callBack($req);
                    if(self::$middleware) {
                        $req->middleware=self::$middleware;
                    } else {
                        $phpData = $mainData;
                    }
                }
                if(isset($req->middleware) && $callBack2) $phpData = $callBack2($req);
                if(is_array($phpData) || is_object($phpData)) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode($phpData);
                }else {
                    echo $phpData;
                }
                self::$validUrl=true;
            }
    }
    }

    public static function get($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"GET");}

    public static function post($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"POST");}

    public static function put($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"PUT");}

    public static function delete($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"DELETE");}

    public static function patch($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"PATCH");}

    public static function options($route,$callBack = false,$callBack2 = false){self::go($route,$callBack,$callBack2,"OPTIONS");}

}
?>