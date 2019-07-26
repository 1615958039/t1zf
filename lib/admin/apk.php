<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"登陆状态失效"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	$type=$_POST["type"];
	
	
	if($type=="edu"){
			
		$title=$_POST['title'];
		$fileid=$_POST['fileid'];
		$apkname=$_POST['apkname'];
		$apkbb=$_POST['apkbb'];
		$is_downsee=$_POST['is_downsee'];
		$icon=$_POST['icon'];
		$img1=$_POST['img1'];
		$img2=$_POST['img2'];
		$img3=$_POST['img3'];
		$img4=$_POST['img4'];
		$url=$_POST['url'];
		$page=$_POST['page'];
		
		if($page!='0' && $page!='1')code(["code"=>"0","message"=>"目前仅开放UI模板0、1"]);
		
		if(mb_strlen($title,'utf-8')<1 || mb_strlen($title,'utf-8')>20)code(["code"=>"0","message"=>"标题长度为1-20字符"]);
		if(mb_strlen($apkname,'utf-8')<1 || mb_strlen($apkname,'utf-8')>10)code(["code"=>"0","message"=>"软件名称长度为1-10字符"]);
		if(mb_strlen($apkbb,'utf-8')<1 || mb_strlen($apkbb,'utf-8')>10)code(["code"=>"0","message"=>"软件名版本长度为1-10字符"]);
		if(mb_strlen($icon,'utf-8')<1 || mb_strlen($icon,'utf-8')>100)code(["code"=>"0","message"=>"图标直链长度为1-100字符"]);
		if(mb_strlen($img1,'utf-8')<1 || mb_strlen($img1,'utf-8')>100)code(["code"=>"0","message"=>"介绍图片1直链长度为1-100字符"]);
		if(mb_strlen($img2,'utf-8')<1 || mb_strlen($img2,'utf-8')>100)code(["code"=>"0","message"=>"介绍图片2直链长度为1-100字符"]);
		if(mb_strlen($img3,'utf-8')<1 || mb_strlen($img3,'utf-8')>100)code(["code"=>"0","message"=>"介绍图片3直链长度为1-100字符"]);
		if(mb_strlen($img4,'utf-8')<1 || mb_strlen($img4,'utf-8')>100)code(["code"=>"0","message"=>"介绍图片4直链长度为1-100字符"]);
		if(mb_strlen($url,'utf-8')<1 || mb_strlen($url,'utf-8')>10)code(["code"=>"0","message"=>"自定义url长度为1-10字符"]);
		
		$res = $sql("SELECT * FROM file WHERE id='".numdecode($fileid)."' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"apk直链ID不存在，请检查是否输入错误！"]);
		
		$res = $sql("SELECT * FROM apk WHERE url='{$url}' AND admin_id<>'".$admin['id']."' ");
		if($res)code(["code"=>"0","message"=>"该自定义url链接已被使用！请更换"]);
		
		
		if($is_downsee!=1 && $is_downsee!=0)code(["code"=>"0","message"=>"请选择是否显示下载数量"]);
		
		$res = $sql("UPDATE  `apk` SET title='{$title}',fileid='{$fileid}',apkname='{$apkname}',apkbb='{$apkbb}',is_downsee='{$is_downsee}',icon='{$icon}',img1='{$img1}',img2='{$img2}',img3='{$img3}',img4='{$img4}',url='{$url}',page='{$page}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"成功！"]);
		else code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='edu_msg'){
		$msg= $_POST['msg'];
		if(mb_strlen($url,'utf-8')>999999)code(["code"=>"0","message"=>"介绍限制999999字符"]);
		$res = $sql("UPDATE  `apk` SET message='{$msg}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"成功！"]);
		else code(["code"=>"0","message"=>"修改失败"]);
	}
	
	
	
	
	
	
	
	
	$res = $sql("SELECT * FROM apk WHERE admin_id='".$admin['id']."' ");
	if(!$res){
		$res = $sql("INSERT INTO `apk` (`admin_id`) VALUES ('".$admin['id']."')");
		$res = $sql("SELECT * FROM apk WHERE admin_id='".$admin['id']."' ");
	}
	unset($res['id']);
	unset($res['admin_id']);
	code(["code"=>"1","message"=>"获取成功！","data"=>$res]);