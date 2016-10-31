<?php
			//  ============= user values ====
			$host = "52.78.109.0";  //  <<<<<<<<<<<<<<<<< YOUR CHATSCRIPT SERVER IP ADDRESS OR HOST-NAME GOES HERE
			$port = 1024;          // <<<<<<< your port number if different from 1024
			$bot  = "";       // <<<<<<< desired botname, or "" for default bot
			//=========================
			// Please do not change anything below this line.
			$null = "\x00";
			 // open client connection to TCP server
				//$userip = ($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']; // get actual ip address of user as his id
				$userip=$data->user_key;
				//$user="°í°´";
				$message=$data->content;
				
				$msg = $userip.$null.$bot.$null.$message.$null;
				// fifth parameter in fsockopen is timeout in seconds
				if(!$fp=fsockopen($host,$port,$errstr,$errno,300))
				{
					trigger_error('Error opening socket',E_USER_ERROR);
				}
				// write message to socket server
				fputs($fp,$msg);
				while (!feof($fp))
				{
					$ret .= fgets($fp, 512);
				}
				// close socket connection
				fclose($fp);
			//=========================
			if(strpos($ret, "³¯¾¾")!=false){//³¯¾¾°¡ Æ÷ÇÔµÇ¾îÀÖÀ¸¸é
				include_once 'Snoopy.class.php';
$snoopy=new snoopy;

$url="https://search.naver.com/search.naver?query=".$ret;
$snoopy->fetch($url);
$txt=$snoopy->results;
//print_r($txt);

$coupon="";
$rex_coupon="/\<img src=(.*)/";
preg_match_all($rex_price,$txt,$coupon);
print_r($coupon[0][0]);
print_r($coupon[0][1]);
				echo <<< EOD
{
			"message":{
				"text" : "$ret"
			},
		   "keyboard": {
			"type": "text"
		  }
		}
EOD;
			}else{
				echo <<< EOD
{
			"message":{
				"text" : "$ret"
			},
		   "keyboard": {
			"type": "text"
		  }
		}
EOD;
			}
							

?>