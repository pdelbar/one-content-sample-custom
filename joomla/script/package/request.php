<?php

//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class oneScriptPackageRequest extends One_Script_Package {

  function get(&$variable, $fromGlobal = "request") {
    $from = false;
    switch ($fromGlobal) {
      case 'get' : $from = $_GET;
        break;
      case 'post' : $from = $_POST;
        break;
      case 'cookie' : $from = $_COOKIE;
        break;
      case 'server' : $from = $_SERVER;
        break;
      case 'request' :
      default : $from = $_REQUEST;
        break;
    }

    return $from[$variable];
  }

  function parse_url($url, $component = -1) {

    return parse_url($url, $component);
  }

  function parse_str($str, &$arr) {
    parse_str($str, $arr);
    return $arr;
  }

}
