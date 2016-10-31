<?php
// php 실행결과response가 xml임을 브라우저에게 알려줌.
//header('Content-type:text/xml');
class NaverProxy {
      public function queryNaver($query) {
		  
		  $query =urlencode($query);
  $client_id = "";
  $client_secret = "";
  $url = "https://openapi.naver.com/v1/search/shop.xml";
  $url = $url."?query=".$query."&display=100&start=1&sort=sim";
  $is_post = true;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_GET, $is_post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $headers = array();
  $headers[] = "X-Naver-Client-Id: ".$client_id;
  $headers[] = "X-Naver-Client-Secret: ".$client_secret;
  
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $data = curl_exec ($ch);
  curl_close ($ch);
  
  return $data;
      }
 }

?>
