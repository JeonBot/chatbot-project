<?php
$data =  json_decode(file_get_contents('php://input'));
//입력을 받아서$data변수에 모두저장

//$data->user_key : 사용자의 고유키
//$data->type : text/photo 로 나뉘어지며 문자인지 미디어인지 구분
//$data->content : 메시지 내용(text일 경우 메시지가,photo일 경우 미디어의 주소가 들어있다.)

//로그 기록----근데 왜 안먹힘ㅠㅠ-----------------
$date=date("Y-m-d h:i:s");
$log_txt=$data->user_key."|".$data->content."|".$date;

$log_dir="/var/www/data/log";
$log_file=fopen("/log.txt","a");
fwrite($log_file, $log_txt."\r\n");
fclose($log_file);
//------------------------------------------

//$status="none";
//mysql과 연결=========================
$db_host="";
$db_user="";
$db_passwd="";
$db_name="";
$conn=mysqli_connect($db_host, $db_user, $db_passwd, $db_name);
if(mysqli_connect_errno($conn)){
	$sqlstatus="연결실패".mysqli_connet_error();
}
//=========================
$query="SELECT * from userStatus WHERE \"$data->user_key\"=userkey";
$result=mysqli_query($conn,$query);
$row = mysqli_fetch_array($result);
if(!$row){
	$query="INSERT INTO userStatus VALUES( \"$data->user_key\",\"$status\")";
    $result=mysqli_query($conn,$query);
}else{
	$status=$row['status'];
}
//=========================


if("$data->type"=="photo"){
	echo <<< EOD
{
		  "message": {
			"text": "죄송합니다(훌쩍) 사진과 관련된 메뉴는 준비중입니다. \\n초기 메뉴로 돌아가려면 \"메뉴\"를, 고객센터 연결을 원하시면 \"고객센터 연결\"을 선택해 주세요!",
			"keyboard": {
				"type": "buttons",
				"buttons": ["메뉴", "고객센터 연결"]
			  }
}
EOD;
}else if("$data->content"== "ㄱ"||"$data->content"== "메뉴"||"$data->content"== "메뉴 선택"){

		$query="UPDATE userStatus set status=\"none\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);

echo <<< EOD
{
			"message":{
				"text" : "(쑥스)원하시는 메뉴를 선택해주세요\ud83d\ude04!"
			},
		   "keyboard": {
			"type": "buttons",
			"buttons": ["L.Chat과 대화하기", "제품 검색", "라이프+", "쿠폰 및 세일 정보", "고객센터"]
		  }
		}
EOD;
//제품 검색=========================
}else if("$data->content"== "L.Chat과 대화하기" || $status=="chatbot"){
$query="UPDATE userStatus set status=\"chatbot\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		if("$data->content"== "L.Chat과 대화하기"){
				echo <<< EOD
{
			"message":{
				"text" : "L.Chat과 대화하기 메뉴에 오신것을 환영합니다\ud83d\ude04\\n제품 검색, 레시피 검색 및 쿠폰과 세일 정보까지 L.Chat에세 물어보세요! \\n대화를 끝내려면 \"메뉴\" 혹은 \"ㄱ\"을 입력하세요."
			},
		   "keyboard": {
			"type": "text"
		  }
		}
EOD;
		}else{
//  ============= user values ====
			$host = "";  //  <<<<<<<<<<<<<<<<< YOUR CHATSCRIPT SERVER IP ADDRESS OR HOST-NAME GOES HERE
			$port = 1024;          // <<<<<<< your port number if different from 1024
			$bot  = "";       // <<<<<<< desired botname, or "" for default bot
			//=========================
			// Please do not change anything below this line.
			$null = "\x00";
			 // open client connection to TCP server
				//$userip = ($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']; // get actual ip address of user as his id
				$userip=$data->user_key;
				//$user="고객";
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
			if($ret=="레시피"){
	 $resultnum = mt_rand(0, 14);
	  $rcp_num=array("000000059217","000000169145","000000209145","000000008858","000000009105","000000003418","000000003508","000000003703","000000059146","000000008858","000000003689","000000009045","000000008258","000000003586","000000079145");
include_once 'Snoopy.class.php';
$snoopy=new snoopy;

$recipeurl="http://www.lottemart.com/recipe/recipeDetail.do?RCP_NO=".$rcp_num[$resultnum];
$snoopy->fetch($recipeurl);
$txt=$snoopy->results;

$ol="";
$rex="/\<h5\>(.*)\<\/h5\>/";
preg_match_all($rex,$txt,$ol);
$rcp_small=strip_tags($ol[0][0]);

$olimg="";
$rex="/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
preg_match_all($rex,$txt,$olimg);
$rcp_img=(string)$olimg[1][18];
//print_r($olimg[0][18]);

$o="";
$rex="/\<p\>\<span\>(.*)\<\/span\>\<\/p\>/";
preg_match_all($rex,$txt,$o);
$rcpresult=strip_tags(implode("\\n\\n(꽃)", $o[0]));
$rcpresult=strip_tags(implode("\\n\\n(꽃)", $o[1]));

echo <<< EOD
{
		  "message": {
			"text": "오늘 이런 메뉴는 어떠세요? \\n L.Chat이 제안하는 오늘의 레시피 <$rcp_small> \\n\\n(하트)$rcpresult ",
			"photo": {
			  "url": "$rcp_img",
			  "width": 640,
			  "height": 480
			},
			"message_button": {
			  "label": "재료 구매하기",
			  "url": "$recipeurl"
			}
		  },
		  "keyboard": {
			"type": "text"			
		  }
}
EOD;
			}else if(strpos($ret,"쇼핑검색")!==FALSE){
		include_once('naverSearch.php');

$mallName=array();
$title=array();
$lprice=array();
$itemlink=array();
$image=array();

$naverproxy = new NaverProxy();

$ret=substr($ret, 12, 85);

 //검색결과를 xml로 받아온다
$resultxml=$naverproxy -> queryNaver($ret);

//xml을 string object로 저장
$result=simplexml_load_string($resultxml)or die("Error: Cannot create object");;

$channel=$result->channel;

foreach($channel->item as $value){
	
	//$test=$value->mallName;
	//if( (strpos($test,$lotteKR)!==FALSE) || (strpos($test,$lotteUS)!==FALSE)){
	array_push($mallName, (string)$value->mallName);
	array_push($title, strip_tags((string)$value->title));
	array_push($lprice, number_format((string)$value->lprice));
	array_push($itemlink, (string)$value->link);
	array_push($image, (string)$value->image);
	
	//}
	

}
//$urllink="http://shopping.naver.com/search/all.nhn?query=".$data->content."&spec=".$status."&mall=2%5E8%5E107396%5E251240%5E319242%5E326857";
$urllinkNospec="http://shopping.naver.com/search/all.nhn?query=".$ret;

if($title[0]){
	echo <<< EOD
{
		  "message": {
			"text": "(하트)$title[0]\\n가격 : $lprice[0]원 \\n판매처 : $mallName[0]",
			"photo": {
			  "url": "$image[0]",
			  "width": 640,
			  "height": 640
			},
			"message_button": {
			  "label": "더 많은 검색 결과",
			  "url": "$urllinkNospec"
			}
		  },
		  "keyboard": {
			"type": "text"
		  }
}
EOD;
}else{
	echo <<< EOD
{
		  "message": {
			"text": "\"$data->content\"에 대한 자세한 검색을 위해 웹페이지로 연결합니다.",
			"message_button": {
			  "label": "검색 결과 확인",
			  "url": "$urllinkNospec"
			}
		  },
		  "keyboard": {
			"type": "text"
		  }
}
EOD;
}
			}
			else if(strpos($ret,"날씨검색")!==FALSE){
		include_once 'Snoopy.class.php';	
			
$ret=substr($ret, 12, 40);

		
$snoopy=new snoopy;

$recipeurl="http://search.daum.net/search?w=tot&DA=YZR&t__nil_searchbox=btn&sug=&sugo=&q=".$ret;
$snoopy->fetch($recipeurl);
$txt=$snoopy->results;
//print_r($txt);


$ol="";
$rex="/\<span class=\"txt_weather\"\>(.*)\<\/span\>/";
preg_match_all($rex,$txt,$ol);
/*
$weather = strip_tags($ol[0][1]);
$weather1 = substr($weather, 0, 85);
$weather2 = str_replace('&nbsp;', ' ', $weather1);
$weather3 = str_replace('  ', ' ', $weather2);
*/
echo <<< EOD
{
		  "message": {
			"text": "날씨를 알려드리고싶지만 지금은 그럴수없네요. 죄송해요.."
		  },
		  "keyboard": {
			"type": "text"
		  }
}
EOD;
			}
			else{//그냥 주고받기
			
			$ret = str_replace('\\n(하트)', '(하트)', $ret);
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
							
		}

	//else if("$data->content"== "L.Chat과 대화하기"){

}else{
	//메뉴 선택=========================
 if("$data->content"== "제품 검색"){
	
		$query="UPDATE userStatus set status=\"search\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		
	/*	while($row = mysqli_fetch_array($result)){
			array_push($test,$row['userkey']);
			array_push($testStatus,$row['status']);
		}*/
		//$result_index=0;
//
echo <<< EOD
{
		  "message": {
			"text": "처음 메뉴로 돌아가려면 \"메뉴\" 혹은 \"ㄱ\"을 입력해 주세요.\\n---------------------------\\n원하시는 제품명을 검색해주세요.\\n(ex)여성 블라우스, 남성 셔츠"
			}
}
EOD;
//검색 결과 띄우기=========================
}else if($status=="search"){
include_once('naverSearch.php');

$lotteKR="롯데";
$lotteUS="lotte"; 

$mallName=array();
$title=array();
$lprice=array();
$itemlink=array();
$image=array();

$naverproxy = new NaverProxy();


 //검색결과를 xml로 받아온다
$resultxml=$naverproxy -> queryNaver($data->content);

//xml을 string object로 저장
$result=simplexml_load_string($resultxml)or die("Error: Cannot create object");;

$channel=$result->channel;

foreach($channel->item as $value){
	
	$test=$value->mallName;
	if( (strpos($test,$lotteKR)!==FALSE) || (strpos($test,$lotteUS)!==FALSE)){
	array_push($mallName, (string)$value->mallName);
	array_push($title, strip_tags((string)$value->title));
	array_push($lprice, number_format((string)$value->lprice));
	array_push($itemlink, (string)$value->link);
	array_push($image, (string)$value->image);
	
	}
	

}
//$urllink="http://shopping.naver.com/search/all.nhn?query=".$data->content."&spec=".$status."&mall=2%5E8%5E107396%5E251240%5E319242%5E326857";
$urllinkNospec="http://shopping.naver.com/search/all.nhn?query=".$data->content."&mall=2%5E8%5E107396%5E251240%5E319242%5E326857";

include ('bitly.php');
$bitlylogin="";
$bitlyapi="";
$bitly=new Bitly($bitlylogin,$bitlyapi);
$shorturl=$bitly->shorten($itemlink[0]);

if($title[0]){
	echo <<< EOD
{
		  "message": {
			"text": "(하트)$title[0]\\n가격 : $lprice[0]원 \\n판매처 : $mallName[0] \\n\\n구매링크 : $shorturl",
			"photo": {
			  "url": "$image[0]",
			  "width": 640,
			  "height": 640
			},
			"message_button": {
			  "label": "더 많은 검색 결과",
			  "url": "$urllinkNospec"
			}
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["제품 검색", "메뉴"]
		  }
}
EOD;
}else{
	echo <<< EOD
{
		  "message": {
			"text": "\"$data->content\"에 대한 자세한 검색을 위해 웹페이지로 연결합니다.",
			"message_button": {
			  "label": "검색 결과 확인",
			  "url": "$urllinkNospec"
			}
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["제품 검색", "메뉴"]
		  }
}
EOD;
}

//라이프 + 메인=========================
}else if("$data->content"== "라이프+"){
	
		$query="UPDATE userStatus set status=\"none\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		
	/*	while($row = mysqli_fetch_array($result)){
			array_push($test,$row['userkey']);
			array_push($testStatus,$row['status']);
		}*/
		//$result_index=0;
echo <<< EOD
{
		  "message": {
			"text": "라이프+ 메뉴입니다.\\n원하시는 메뉴를 선택해주세요."
			},
		  "keyboard": {
			"type": "buttons",
			"buttons": ["오늘의 레시피", "메이크업 팁", "패션 팁"]
		  }
}
EOD;
//라이프+ 레시피 가져오기
}else if("$data->content"== "오늘의 레시피"){
	
	  $resultnum = mt_rand(0, 14);
	  $rcp_num=array("000000059217","000000169145","000000209145","000000008858","000000009105","000000003418","000000003508","000000003703","000000059146","000000008858","000000003689","000000009045","000000008258","000000003586","000000079145");
include_once 'Snoopy.class.php';
$snoopy=new snoopy;

$recipeurl="http://www.lottemart.com/recipe/recipeDetail.do?RCP_NO=".$rcp_num[$resultnum];
$snoopy->fetch($recipeurl);
$txt=$snoopy->results;

$ol="";
$rex="/\<h5\>(.*)\<\/h5\>/";
preg_match_all($rex,$txt,$ol);
$rcp_small=strip_tags($ol[0][0]);

$olimg="";
$rex="/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
preg_match_all($rex,$txt,$olimg);
$rcp_img=(string)$olimg[1][18];
//print_r($olimg[0][18]);

$o="";
$rex="/\<p\>\<span\>(.*)\<\/span\>\<\/p\>/";
preg_match_all($rex,$txt,$o);
$rcpresult=strip_tags(implode("\\n\\n(꽃)", $o[0]));
$rcpresult=strip_tags(implode("\\n\\n(꽃)", $o[1]));

echo <<< EOD
{
		  "message": {
			"text": "오늘의 레시피 : $rcp_small \\n(하트)$rcpresult ",
			"photo": {
			  "url": "$rcp_img",
			  "width": 640,
			  "height": 480
			},
			"message_button": {
			  "label": "재료 구매하기",
			  "url": "$recipeurl"
			}
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["제품 검색", "메뉴"]
		  }
}
EOD;
}else if("$data->content"== "메이크업 팁"){

echo <<< EOD
{
		  "message": {
			"text": "둥근 얼굴을 달갈형 얼굴로 바꾸기\\n 사용 제품 : 컨실러, 하이라이터 등",
			"photo": {
			  "url": "http://bimage.interpark.com/goods_image/9/9/1/7/223829917b.jpg",
			  "width": 640,
			  "height": 640
			},
			"message_button": {
			  "label": "사용 제품 구매하기",
			  "url": "http://ellotte.com/display/viewDispShop.lotte?disp_no=5494430&tracking=EL_HEADER_CATE_01_01"
			}
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["제품 검색", "메뉴"]
		  }
}
EOD;
}else if("$data->content"== "패션 팁"){

echo <<< EOD
{
		  "message": {
			"text": "패션 팁 가져오기"
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["제품 검색", "메뉴"]
		  }
}
EOD;
}else if("$data->content"== "쿠폰 및 세일 정보"){
	
		$query="UPDATE userStatus set status=\"saleinfo\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		
	/*	while($row = mysqli_fetch_array($result)){
			array_push($test,$row['userkey']);
			array_push($testStatus,$row['status']);
		}*/
		//$result_index=0;
echo <<< EOD
{
		  "message": {
			"text": "원하시는 계열사를 선택해 주세요."
			},
		  "keyboard": {
			"type": "buttons",
			"buttons": ["롯데마트", "롯데홈쇼핑", "롯데닷컴", "롯데백화점"]
		  }
}
EOD;
//쿠폰 및 세일 정보 하위 메뉴
}else if($status=="saleinfo"){
		$query="UPDATE userStatus set status=\"none\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		
		switch($data->content){
			case "롯데마트":
				$url="http://www.lottemart.com/event/coupon.do?SITELOC=KA031";
				$msg="현재 롯데마트에서는(하하)\\n(하트)명절세트 특별쿠폰 최대 7% 할인\\n\\n(하트)회원가입만 해도 쿠폰 2종(최대 20% 할인) 증정!";
				$img="http://image.lottemart.com/images/staticimg/coupon/C00000001945790_200_110.jpg";
				break;
				
			case "롯데홈쇼핑":
				$url="http://www.lotteimall.com/event/viewEventZone.lotte?tlog=00100_11";
				$img="http://image.lotteimall.com/upload/corner/192/5000020/5000020_192_0_30_512_20160809140534.jpg";
			$msg="현재 롯데홈쇼핑(롯데i몰)에서는(하하)\\n(하트)KB국민카드 청구할인 모바일 앱 쿠폰 기회! \\n\\n(하트)방송알림 이벤트 - 알림신청 후 구매하면 3천원 플러스쿠폰 증정! \\n\\n(하트)롯데홈쇼핑 - 3천원 할인쿠폰 받고 던킨도너츠 세트 받으세요! \\n\\n(하트)할인혜택 팡팡 - 매일매일 플러스쿠폰!";
				break;
				
			case "롯데닷컴":
				$url="http://www.lotte.com/event/viewEventZone.lotte?tracking=MH_CORNER_08";
				$msg="롯데닷컴의 쿠폰 및 세일, 이벤트 내역입니다.";
				$img="http://image.lotte.com/upload/display/corner/5000953_47855_0_30_13008698_386.jpg";
				break;
			case "롯데백화점":
				$url="http://ellotte.com/event/viewRegularCafeMain.lotte?tracking=MH_CORNER_05";
				$msg="롯데백화점(엘롯데)의 쿠폰 및 세일, 이벤트 내역입니다.";
				$img="http://image.lotte.com/lotte/images/mylotte/goodsbenefit/img_banner_nologin_201408.jpg";
			
				break;
			
		}
echo <<< EOD
{
		  "message": {
			"text": "$msg",
			"photo": {
			  "url": "$img",
			  "width": 640,
			  "height": 450
			},
			"message_button": {
			  "label": "쿠폰/세일 바로가기",
			  "url": "$url"
			}
		  },
		  "keyboard": {
			"type": "buttons",
			"buttons": ["쿠폰 및 세일 정보", "메뉴", "고객센터 연결"]
		  }
}
EOD;
//라이프 + 메인=========================
}else if("$data->content"== "설문조사"){
	
		$query="UPDATE userStatus set status=\"none\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		

echo <<< EOD
{
		  "message": {
			"text": "설문조사 메뉴입니다. 추후 Lime과 연결 가능한 기능입니다."
			},
		  "keyboard": {
			"type": "buttons",
			"buttons": ["메뉴"]
		  }
}
EOD;
//고객센터, 고객센터 연결
}else if("$data->content"== "고객센터"||"$data->content"== "고객센터 연결"){
	
		$query="UPDATE userStatus set status=\"none\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
		

echo <<< EOD
{
		  "message": {
			"text": "롯데 계열사의 고객센터 정보입니다. \\n\\n(하트)롯데마트 \\nwww.lottemart.com (02-2145-8000)\\n\\n(하트)롯데슈퍼 \\nwww.lottesuper.co.kr (1688-9600)\\n\\n(하트)롯데백화점 \\nwww.ellotte.com (02-771-2500) \\n\\n(하트)롯데닷컴 \\nwww.lotte.com (1577-1110) \\n\\n(하트)롯데홈쇼핑 \\nwww.lotteimall.com (1899-4000)"
			},
		  "keyboard": {
			"type": "buttons",
			"buttons": ["메뉴"]
		  }
}
EOD;
//라이프+ 레시피 가져오기 //레시피 쿠폰 날씨 api 연결 검색, 검색이 안되면 네이버 검색결과 연결
}else{
	
	echo <<< EOD
{
		  "message": {
			"text": "죄송합니다(훌쩍) 초기 메뉴로 돌아가려면 \"메뉴\"를, 고객센터 연결을 원하시면 \"고객센터 연결\"을 선택해 주세요!",
			"keyboard": {
				"type": "buttons",
				"buttons": ["메뉴", "고객센터 연결"]
			  }
}
EOD;

}
}




?>