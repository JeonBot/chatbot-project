<?php
include_once 'Snoopy.class.php';

$snoopy=new snoopy;

$recipeurl="http://search.daum.net/search?w=tot&DA=YZR&t__nil_searchbox=btn&sug=&sugo=&q=%EA%B8%88%EC%B2%9C%EA%B5%AC+%EB%82%A0%EC%94%A8";
$snoopy->fetch($recipeurl);
$txt=$snoopy->results;
//print_r($txt);


$ol="";
$rex="/\<span class=\"txt_weather\"\>(.*)\<\/span\>/";
preg_match_all($rex,$txt,$ol);
$weather.=strip_tags($ol[1][0]);
$weather = substr($weather, 0, 85);
$weather = str_replace('&nbsp;', ' ', $weather);
$weather = str_replace('  ', ' ', $weather);

$ret="날씨검색 금천구 날씨";

print_r($weather);
echo strpos($ret,"날씨검색");
?>

<!-- 
else if("$data->content"== "날씨"){
	
	 include_once 'Snoopy.class.php';
$snoopy=new snoopy;

$recipeurl="http://search.daum.net/search?w=tot&DA=YZR&t__nil_searchbox=btn&sug=&sugo=&q=%EA%B8%88%EC%B2%9C%EA%B5%AC+%EB%82%A0%EC%94%A8";
$snoopy->fetch($recipeurl);
$txt=$snoopy->results;
//print_r($txt);


$ol="";
$rex="/\<span class=\"txt_weather\"\>(.*)\<\/span\>/";
preg_match_all($rex,$txt,$ol);
$weather.=strip_tags($ol[1][0]);
$weather = substr($weather, 0, 85);
$weather = str_replace('&nbsp;', ' ', $weather);
$weather = str_replace('  ', ' ', $weather);

echo <<< EOD
{
		  "message": {
			"text": "$weather"
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["메뉴"]
		  }
}
EOD;
}
-->