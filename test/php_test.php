 <?php 
 // don't need to specify http, it's the default protocol 
 $hostname = "http://52.78.109.0'"; 
 $port     = 3305; 

 // create and configure the client socket 
 $fp = fsockopen($hostname, $port); 
 // optional: $error_number, $error_string, $connect_timeout 

 if ($fp) { 
    // seconds to wait for i/o operations 
    stream_set_timeout($fp, 30);
     
    // send request headers 
    fwrite($fp, "GET / HTTP/1.1\r\n"); 
    fwrite($fp, "Host: $hostname\r\n");

    // Accept, User-Agent, Referer, etc.  
    fwrite($fp, $additional_headers); 

    fwrite($fp, "Connection: close\r\n"); 
     
    // read response 
  $response = ""; 
    while (!feof($fp)) { 
        $response .= fgets($fp, 128); 
    } 
  echo $response; 
     
    // close the socket 
    fclose($fp); 
 } 
 ?>