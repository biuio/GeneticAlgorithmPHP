<?php

ini_set('display_errors', 1);            //错误信息  
ini_set('display_startup_errors', 1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
set_time_limit(0); //脚本永不超时
define('GA', dirname(__FILE__));
require './Autoload.php';
$line = new Ga\Pix\Line();
