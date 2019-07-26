<?php
	include("function.php");
	
	function jstw($text){
		$a = '<script>alert("'.$text.'");history.back(-1)</script>';
		die($a);
	}
	
	$type=$_GET['type'];
	if($type=='getinfo'){
		$key = keydecode($_GET['key']);
		$res = $sql("SELECT * FROM file WHERE id='{$key}' ");
		if(!$res)code(["code"=>"0","message"=>"找不到该文件"]);
		code(["code"=>"1","message"=>"yes",'filemsg'=>$res['msg'],'filename'=>htmlentities($res['filename']),'filesize'=>$res['filesize']]);
	}else if($type=='keydown'){
		$key = keydecode($_GET['key']);
		$id = $key;
		$randcode = $_GET['code'];
		if($randcode!=$_SESSION['code'])jstw("验证码输入错误！");
		
		if($_SESSION['downtime']<time())jstw("已到期");
			
			
		
		
		
		
		$res = $sql("SELECT * FROM file WHERE id='{$key}' ");
		if(!$res)jstw("源文件已失效");
		
		$ip=ip();
		$log = $sql("SELECT * FROM download_log WHERE ip='{$ip}' AND fileid='{$id}' AND istext='0' ORDER BY id desc LIMIT 0,1  ");
		if(strtotime($log['addtime'])+86400<strtotime("now") || !$log){
			$down = $sql("INSERT INTO `download_log` (`ip`,`addtime`,`fileid`) VALUES ('{$ip}','{$date}','{$id}')");
			$up = $sql("UPDATE  `file` SET download=download+1 WHERE id='{$id}' ");
		}
		$name = explode('.',$res['filename']);
		$i=count($name)-1;
		$local_file = '../file/'.$res['md5'].'.'.$name[$i];
		if( !file_exists($local_file) || !is_file($local_file) )jstw("找不到源文件");
		header ( 'Content-Description: File Transfer' );
		header ( 'Content-Type: application/octet-stream' );
		header ( 'Content-Disposition: attachment; filename=' . basename ( $res['filename'] ));
		header ( 'Content-Transfer-Encoding: binary' ); 
		header ( 'Expires: 0' ); 
		header ( 'Cache-Control: must-revalidate' );
		header ( 'Pragma: public' ); 
		header ( 'Content-Length: ' . $res['filesize'] );
		
		ob_clean (); 
		flush (); 
		readfile ( $local_file );
		exit;
	}
	
	
	
	
	
	
	
	
	
	function html($text){
		$a = '<h1>'.$text.'</h1>';
		die($a);
	}
	
	$id = numdecode($_GET['v']);
	
	
	if(!$id)html('文件不存在或已被管理员下架');
	$res = $sql("SELECT * FROM file WHERE id='{$id}' ");
	if(!$res || $res['ispay']==0)html('文件不存在或已被管理员下架');
	
	//	下载统计
	$ip=ip();
	$log = $sql("SELECT * FROM download_log WHERE ip='{$ip}' AND fileid='{$id}' AND istext='0' ORDER BY id desc LIMIT 0,1 ");
	if(strtotime($log['addtime'])+86400<strtotime("now") || !$log){
		$down = $sql("INSERT INTO `download_log` (`ip`,`addtime`,`fileid`) VALUES ('{$ip}','".$date."','{$id}')");
		$up = $sql("UPDATE  `file` SET download=download+1 WHERE id='{$id}' ");
		if(!$down || !$up)html('文件不存在或已被管理员下架');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	$name = explode('.',$res['filename']);
	$i=count($name)-1;
	$local_file = '../file/'.$res['md5'].'.'.$name[$i];
	if( !file_exists($local_file) || !is_file($local_file) )html('文件不存在或已被管理员下架');
	header ( 'Content-Description: File Transfer' );
	header ( 'Content-Type: application/octet-stream' );
	header ( 'Content-Disposition: attachment; filename=' . basename ( $res['filename'] ));
	header ( 'Content-Transfer-Encoding: binary' ); 
	header ( 'Expires: 0' ); 
	header ( 'Cache-Control: must-revalidate' );
	header ( 'Pragma: public' ); 
	header ( 'Content-Length: ' . $res['filesize'] );
	ob_clean (); 
	flush (); 
	readfile ( $local_file );
	exit;