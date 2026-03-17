<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../includes/common.php';
require_once ('/vs/www/php-bin/app-config/popclock/bootstrap.inc.php');

#Curl call to api to get population data
$baseCurl = _APPROOT_ . "data/population";


if(strpos($_SERVER["HTTP_HOST"],'web43') !== false){   
	$baseCurl .= '.php';
	$usUrl = "http:" .$baseCurl . "/us";	
	$worldUrl = "http:" .$baseCurl . "/world";
}else if(strpos($_SERVER["HTTP_HOST"],'wdob') !== false){
    
    $baseCurl .= '.php';
    $usUrl = "https:" .$baseCurl . "/us";
    $worldUrl = "https:" .$baseCurl . "/world";   
}

$usPopCall = `curl -H "Content-Type: application/json" '$usUrl'`;
$worldPopCall= `curl -H "Content-Type: application/json" '$worldUrl'`;
$usPopJson=  json_decode($usPopCall);
$worldPopJson=  json_decode($worldPopCall);

#Get U.S. population from decoded json
$usPop = $usPopJson->us->population;

#Get World population from decoded json
$worldPop = $worldPopJson->world->population;

$u_agent = $_SERVER['HTTP_USER_AGENT'];
#dynamically create rss page

header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>'; 
echo "<rss version='2.0'>";
    echo"<channel>";
        echo"<title>Census Bureau Population Estimates:  PopClocks</title>";
        echo"<link>". _APPROOT_ . "</link>";
        echo"<description>Daily US and World Population Estimates from the US Census Bureau</description>";
        echo"<language>en-us</language>";
        echo"<copyright>None</copyright>";
        echo"<docs>http://blogs.law.harvard.edu/tech/rss</docs>";
        echo"<item>";
            echo"<title>US Population Estimate:";
            #Insert U.S. population value
                echo" $usPop on ". now;
            echo"</title>";
            echo"<link>". _APPROOT_ . "</link>";
            echo"<category>Federal Government Statistics</category>";
	echo"</item>";
	echo"<item>";
            echo"<title>World Population Estimate:";
            #Insert World Population value
                    echo" $worldPop on " . now;
            echo"</title>";
            echo"<link>". _APPROOT_ . "</link>";
            echo"<category>Federal Government Statistics</category>";
	echo"</item>";
    echo"</channel>";
echo"</rss>";
