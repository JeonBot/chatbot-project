<?php
$status="none";

//mysql�� ����=========================
$db_host="";
$db_user="";
$db_passwd="";
$db_name="";
$conn=mysqli_connect($db_host, $db_user, $db_passwd, $db_name);
if(mysqli_connect_errno($conn)){
	$sqlstatus="�������".mysqli_connet_error();
}
//=========================
$query="SELECT * from userStatus WHERE \"$data->user_key\"=userkey";
$result=mysqli_query($conn,$query);
$row = mysqli_fetch_array($result);
if(!$row['userkey']){
	$query="INSERT INTO userStatus VALUES( \"$data->user_key\",\"$status\")";
    $result=mysqli_query($conn,$query);
}else{
	$query="UPDATE userStatus set status=\"$status\" where userkey=\"$data->user_key\"";
        $result=mysqli_query($conn,$query);
}

	
echo <<< EOD
{
		   "keyboard": {
			"type": "buttons",
			"buttons": ["L.Chat�� ��ȭ�ϱ�", "��ǰ �˻�", "������+", "���� �� ���� ����", "��������", "������"] 
			}
		}
EOD;
?>