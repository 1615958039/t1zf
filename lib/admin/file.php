<?php
	
	include("../function.php");
	if(!$admin)code(["code"=>"0","message"=>"请先登陆"]);
	$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	$type=$_GET["type"];
	if($type=='getinfo'){
		$list=array();
		$i=0;
		$ii='';
		$name='';
		$ispng=array('png','jpg','jpeg');
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' ORDER BY id DESC","list");
		foreach($res as $val){
			$fileurl='';
			$name = explode('.',$val['filename']);
			$ii=count($name)-1;
			
			if(in_array($name[$ii],$ispng)){
				$fileurl=$val['md5'].'.'.$name[$ii];
				$name[$ii]='img';
			}
			$list[$i++] = ['id'=>$val['id'],'name'=>htmlentities($val['filename']),'type'=>$name[$ii],'size'=>$val['filesize'],'addtime'=>$val['addtime'],'down'=>$val['download'],'fileurl'=>$fileurl,'ispay'=>$val['ispay'],'filemsg'=>$val['msg'],'filekey'=>keycode($val['id'])];
		}
		
		$havefile = $sql("SELECT SUM(filesize) FROM file WHERE admin_id='".$admin['id']."' ");
		$h = $havefile['SUM(filesize)']/$admin['filemax']*100;
		$h=round($h,2);
		
		code(["code"=>"1","message"=>"获取成功！",'list'=>$list,
			'model'=>['maxsize'=>$admin['filemax'],'jdt_width'=>$h]
		]);
		
	}else if($type=='pay'){
		$id = $_GET['id'];
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if(!$res)code(["code"=>"0","message"=>"不存在该文件"]);
		if($res['ispay']==1)code(["code"=>"0","message"=>"该文件已经付款了哦~"]);
		$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
		if($admin['money']<1)code(["code"=>"0","message"=>"您的余额不足哦！请先充值余额再试"]);
		
		$uplog = $sql("INSERT INTO `log_admin_money` (`admin_id`,`money`,`addtime`,`message`) VALUES ('".$admin['id']."','-1','{$date}','开通直链')");
		$upadmin = $sql("UPDATE  `admin` SET money=money-1 WHERE id='".$admin['id']."' ");
		$upfile = $res = $sql("UPDATE  `file` SET ispay='1' WHERE  id='{$id}' ");
		
		if($uplog && $upadmin && $upfile)code(["code"=>"1","message"=>"购买成功！"]);
		
		code(["code"=>"0","message"=>"链接服务器失败！"]);
	}else if($type=='geturl'){
		$id = $_GET['id'];
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if(!$res)code(["code"=>"0","message"=>"不存在该文件"]);
		if($res['ispay']==0)code(["code"=>"0","message"=>"该文件还未开通直链~"]);
		
		$name = explode('.',$res['filename']);
		$ii=count($name)-1;
		$isavi = array('3gp','avi','mp4');
		if(in_array($name[$ii],$isavi)){
			code(["code"=>"1","message"=>"获取成功！",'url'=>'http://t1zf.com/file/'.$res['md5'].".".$name[$ii]]);
		} 
		 
		code(["code"=>"1","message"=>"获取成功！",'url'=>'http://t1zf.com/d?v='.numcode($res['id'])]);
		
		
	}else if($type=='changename'){
		$id = $_POST["id"];
		$filename = $_POST["filename"];
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if(!$res)code(["code"=>"0","message"=>"不存在该文件"]);
		if(mb_strlen($filename,'UTF8')>90)code(["code"=>"0","message"=>"文件名请勿过长"]);
		$name = explode('.',$res['filename']);
		$i=count($name)-1;
		$type = $name[$i];
		$res = $sql("UPDATE  `file` SET filename='".$filename.".".$type."' WHERE id='{$id}' ");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败！"]);
	}else if($type=='del'){
			
		$id = $_POST["id"];
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if(!$res)code(["code"=>"0","message"=>"不存在该文件"]);
		$name = explode('.',$res['filename']);
		$i=count($name)-1;
		$type = $name[$i];
		$del = $sql("DELETE FROM file WHERE id='{$id}' AND admin_id='".$admin['id']."' ");
		if(unlink('../../file/'.$res['md5'].'.'.$type) && $del)code(["code"=>"1","message"=>"删除成功！"]);
		code(["code"=>"0","message"=>"删除失败"]);
		
	}else if($type=="changemsg"){
		$id = keydecode($_POST['filekey']);
		$msg = htmlentities($_POST['filemsg']);
		if(mb_strlen($msg,'UTF8')>200)code(["code"=>"0","message"=>"文件说明仅限200字符"]);
		$res = $sql("SELECT * FROM file WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if(!$res)code(["code"=>"0","message"=>"不存在该文件"]);
		$res = $sql("UPDATE  `file` SET msg='$msg' WHERE id='$id' AND admin_id='".$admin['id']."' ");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"链接服务器失败！"]);
	}
	
	
	
	//上传文件代码
	
	$name = explode('.',$_FILES["file"]["name"]);
	$i=count($name)-1;
	if(!in_array($name[$i],$p_type))code(["code"=>"0","message"=>"暂不支持该文件格式！"]);
	if($_FILES["file"]["error"]>0)code(["code"=>"0","message"=>"上传失败！未知原因"]);
	if($_FILES["file"]["size"]<1024)code(["code"=>"0","message"=>"单个文件最小需大于1K"]);
	if($_FILES["file"]["size"]>20971520)code(["code"=>"0","message"=>'单个文件最大上传限制为20M']);
	
	$havefile = $sql("SELECT SUM(filesize) FROM file WHERE admin_id='".$admin['id']."' ");
	
	$nowfilemax=$havefile['SUM(filesize)']+$_FILES["file"]["size"];
	if($nowfilemax>$admin['filemax'])code(["code"=>"0","message"=>"云盘内存空间不足！"]);
	
	$md5 = md5($_FILES["file"]["name"].date("Y-m-d H:i:s").strtotime("now").$admin['id'].rand(99,9999));
	$newfilename = $md5.'.'.$name[$i];
	
	
	
	if(!move_uploaded_file($_FILES["file"]["tmp_name"],"../../file/".$newfilename))code(["code"=>"0","message"=>"上传文件失败！服务器内存空间不足"]);
	
	$res = $sql("INSERT INTO `file`
		(`admin_id`,`filename`,`filesize`,`addtime`,`md5`) 
		VALUES 
		('".$admin['id']."','".$_FILES["file"]["name"]."','".$_FILES["file"]["size"]."','$date','{$md5}')
	");
	if($res)code(["code"=>"1","message"=>"上传文件成功！"]);
	else code(["code"=>"0","message"=>"上传失败，未知原因"]);
	
	
	
	
	
	
	
	
	