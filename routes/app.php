<?php
$cors(['origin'   => '*','headers'  => '*',]);
/* $model(['model1','model2',....]); Your custom models */
$controller(['controller','xml/xmlcontroller']);


app::get('get-xml-lists',fn($request) => xmlcontroller::getXMLists($request));

app::get('create-xml/:uid',fn($request) => xmlcontroller::createXML($request));

app::get('get-xml/:category',fn($request) => xmlcontroller::getXML($request));

app::get('get-product/:id',fn($request) => xmlcontroller::getProduct($request));