<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"cookie无效"]);
	
	$type = $_GET['type'];
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	if($type=="getList"){
		$res = $sql("SELECT * FROM textlist WHERE admin_id='".$admin['id']."'","list");
		$json = array();
		$i=0;
		foreach($res as $val){
			$json[$i++]=array(
				'id'=>listlistcode($val['id']),
				'msg'=>$val['msg'],
				'textinfo'=>$val['textinfo'],
				'addtime'=>$val['addtime'],
				'see'=>$val['see']
			);
		}
		code(["code"=>"1","message"=>"获取成功！",'serverip'=>$serverip,"data"=>$json]);
	}

	
	else if($type=="add"){
		$msg = $_POST["msg"];
		$see = $_POST["see"];
		$textinfo = $_POST["textinfo"];
		if(mb_strlen($msg,'utf-8')>10 || $msg=='')code(["code"=>"0","message"=>"标题备注不符合规范"]);
		if(mb_strlen($textinfo,'utf-8')>999999 || $textinfo=='')code(["code"=>"0","message"=>"标题备注不符合规范"]);
		if(!$see || $see=='')$see='0';
		if($see>999999|| $see<0)code(["code"=>"0","message"=>"数值错误！"]);
		if($sql("SELECT count(*) FROM textlist WHERE admin_id='".$admin['id']."'")>=30)code(["code"=>"0","message"=>"您已到达文档上限"]);
		
		$res = $sql("INSERT INTO `textlist` (`admin_id`,`msg`,`textinfo`,`addtime`,`see`) VALUES ('".$admin['id']."','{$msg}','{$textinfo}','{$date}','{$see}')");
		
		if($res)code(["code"=>"1","message"=>"添加成功！"]);
		else code(["code"=>"0","message"=>"添加失败！"]);
	}

	
	else if($type=='getinfo'){
		$id=textlistdecode($_POST["id"]);
		//订单编辑
		$res = $sql("SELECT * FROM textlist WHERE id='{$id}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"抱歉了您勒，查无结果"]);
		code(["code"=>"1","message"=>"获取成功！","msg"=>$res['msg'],"textinfo"=>$res['textinfo'],"see"=>$res['see']]);
	} 
	
	else if($_POST['type']=="del"){
		
		$id=textlistdecode($_POST["id"]);
		$res = $sql("SELECT * from textlist WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
		if($res){
			$res = $sql("DELETE FROM textlist WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
			if($res)code(["code"=>"1","message"=>"删除成功！"]);
			else code(["code"=>"0","message"=>"删除失败，权限不足"]);
		}else{
			code(["code"=>"0","message"=>"无权限！"]);
		}
		
	}
	
	
	else if($type=='sel_edu'){
		$msg = $_POST["msg"];
		$see = $_POST["see"];
		$textinfo = $_POST["textinfo"];
		if(mb_strlen($msg,'utf-8')>10 || $msg=='')code(["code"=>"0","message"=>"标题备注不符合规范"]);
		if(mb_strlen($textinfo,'utf-8')>999999 || $textinfo=='')code(["code"=>"0","message"=>"标题备注不符合规范"]);
		if(!$see || $see=='')$see='0';
		if($see>999999|| $see<0)code(["code"=>"0","message"=>"数值错误！"]);
		$id = textlistdecode($_POST['id']);
		$res = $sql("UPDATE  `textlist` SET msg='{$msg}',see='{$see}',textinfo='{$textinfo}' WHERE id='{$id}' AND admin_id='".$admin['id']."' ");
		
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		else code(["code"=>"0","message"=>"修改失败！"]);
	}
