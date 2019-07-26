<?php
	
	
	
	include("../function.php");
	
	
	
	if(!$admin)code(["code"=>"-1","message"=>"cookie无效"]);
	
	$type = $_GET['type'];
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	if($type=="getList"){
		$res = $sql("SELECT * FROM pay_goods WHERE admin_id='".$admin['id']."'","list");
		$json = array();
		$i=0;
		foreach($res as $val){
			
			if($val['dowhat']=="用户系统充值余额"){
				$val['money'] = json_decode($val['doconfig'],TRUE)['money'];	
			}else if($val['dowhat']=="用户系统购买VIP"){
				$val['money'] = json_decode($val['doconfig'],TRUE)['money'];
			}else if($val['dowhat']=='自动发货'){
				$val['sell'] = $sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='".$val['id']."' AND issell='1' ");
				$val['havenum'] = $sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='".$val['id']."' AND issell='0' ");
				
			}
			
			
			if($val['zt']=='0' && $val['havenum']>0)$zt="正常";
			else if($val['havenum']==0)$zt='缺库存';
			else if($val['zt']==1)$zt='已下架';
			
			
			
			
			
			
			$json[$i++]=array(
				'id'=>$val['id'],
				'title'=>$val['title'],
				'money'=>$val['money'],
				'addtime'=>$val['addtime'],
				'havenum'=>$val['havenum'],
				'sell'=>$val['sell'],
				'dowhat'=>$val['dowhat'],
				'addmsg'=>htmlentities(mb_substr($val['addmsg'],0,10,'utf-8')).'...',
				'zt'=>$zt,
			);
		}
		code(["code"=>"1","message"=>"获取成功！",'serverip'=>$serverip,"data"=>$json]);
	}

	
	else if($type=="add"){
		
		if($sql("SELECT count(*) from pay_goods WHERE admin_id='".$admin['id']."' ")>=20)code(["code"=>"0","message"=>"商品最多添加20个"]);
		
		$title = $_POST['add_title'];
		$money = $_POST['add_money'];
		$havenum = (int)$_POST['add_havenum'];
		$addmsg = $_POSt['add_msg'];
		$dowhat = $_POST['dowhat'];
		
		if(mb_strlen($title,'utf-8')>10 || mb_strlen($title,'utf-8')<2)code(["code"=>"0","message"=>"商品标题为2-10个字符"]);
		if(!is_money($money))code(["code"=>"0","message"=>"金额错误，9999.99-0.01"]);
		if($havenum>99999999 || $havenum<1){
			if($dowhat!="自动发货")code(["code"=>"0","message"=>"商品库存须在1-99999999之间"]);
		}
		if(mb_strlen($addmsg,'utf-8')>200)code(["code"=>"0","message"=>"商品介绍不得超过200字"]);
		
		
		
		
		if($dowhat=='POST对接'){
			$get = $_POST['add_geturl'];
			$post = $_POST['add_getpost'];
			$cookie = $_POST['add_getcookie'];
			if(!$get)code(["code"=>"0","message"=>"必须配置url参数"]);
			if(mb_strlen($get,'utf-8')>300 || mb_strlen($get,'utf-8')<10)code(["code"=>"0","message"=>"请输入正确的url,10-100字符"]);
			if(mb_strlen($post,'utf-8')>300)code(["code"=>"0","message"=>"post内容需小于300字符"]);
			if(mb_strlen($cookie,'utf-8')>300)code(["code"=>"0","message"=>"cookie内容需小于300字符"]);
			if(sj($get,"http://","/")=='' && sj($get,'https://','/')=='')code(["code"=>"0","message"=>"网站需填写协议，http或https"]);
			
			//if(!file_get_contents($get))code(["code"=>"0","message"=>"无法链接该网站！请联系站长添加白名单"]);
			
			/* 拼接json */
			
			$arr =['get'=>$get,'post'=>$post,'cookie'=>$cookie];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("INSERT INTO `pay_goods` 
				(`admin_id`,`title`,`money`,`addtime`,`addmsg`,`dowhat`,`doconfig`,`havenum`) 
				VALUES 
				('".$admin['id']."','{$title}','{$money}','{$date}','{$addmsg}','{$dowhat}','{$doconfig}','{$havenum}')
			");
			
			if($res)code(["code"=>"1","message"=>"添加成功！"]);
			else code(["code"=>"0","message"=>"添加失败！"]);
			
		}else if($dowhat=='亿乐社区'){
			$ylurl = $_POST["add_ylurl"];
			$yltkid = (int)$_POST["add_yltkid"];
			$ylkey = $_POST["add_ylkey"];
			$ylgid = (int)$_POST["add_ylgid"];
			$ylnum = $_POST["add_ylnum"];
			
			if(mb_strlen($ylurl,'utf-8')<5 || mb_strlen($get,'utf-8')>100)code(["code"=>"0","message"=>"请输入正确的亿乐域名"]);
			if(is_set($ylurl,'/'))code(["code"=>"0","message"=>"亿乐域名勿填http://及/",]);
			if($yltkid<1)code(["code"=>"0","message"=>"请输入正确的tokenid"]);
			if(mb_strlen($ylkey,'utf-8')!=32)code(["code"=>"0","message"=>"请输入正确的32位key"]);
			if($ylgid<1)code(["code"=>"0","message"=>"商品ID不正确"]);
			if(!is_money($ylnum))code(["code"=>"0","message"=>"下单基数数值错误！"]);
			
			$arr =['api'=>$ylurl,'tokenid'=>$yltkid,'key'=>$ylkey,'gid'=>$ylgid,'num'=>$ylnum];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("INSERT INTO `pay_goods` 
				(`admin_id`,`title`,`money`,`addtime`,`addmsg`,`dowhat`,`doconfig`,`havenum`) 
				VALUES 
				('".$admin['id']."','{$title}','{$money}','{$date}','{$addmsg}','{$dowhat}','{$doconfig}','{$havenum}')
			");
			
			if($res)code(["code"=>"1","message"=>"添加成功！"]);
			else code(["code"=>"0","message"=>"添加失败！"]);
			
		}else if($dowhat=='用户系统充值余额'){
			$addmoney=$_POST['add_czmoney'];
			if(!is_money($addmoney))code(["code"=>"0","message"=>"请输入正确的金额比例"]);
			
			$arr =['money'=>$addmoney];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("INSERT INTO `pay_goods` 
				(`admin_id`,`title`,`money`,`addtime`,`addmsg`,`dowhat`,`doconfig`,`havenum`) 
				VALUES 
				('".$admin['id']."','{$title}','{$money}','{$date}','{$addmsg}','{$dowhat}','{$doconfig}','{$havenum}')
			");
			if($res)code(["code"=>"1","message"=>"添加成功！"]);
			else code(["code"=>"0","message"=>"添加失败！"]);
			
		}else if($dowhat=='用户系统购买VIP'){
			
			
			$addvip=$_POST['add_czvip'];
			
			
			if(!is_money($addvip))code(["code"=>"0","message"=>"请输入正确的VIP价格"]);
			
			$arr =['money'=>$addvip];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			$res = $sql("INSERT INTO `pay_goods` 
				(`admin_id`,`title`,`money`,`addtime`,`addmsg`,`dowhat`,`doconfig`,`havenum`) 
				VALUES 
				('".$admin['id']."','{$title}','{$money}','{$date}','{$addmsg}','{$dowhat}','{$doconfig}','{$havenum}')
			");
			if($res)code(["code"=>"1","message"=>"添加成功！"]);
			else code(["code"=>"0","message"=>"添加失败！"]);
			
		}else if($dowhat=='自动发货'){
			
			$res = $sql("INSERT INTO `pay_goods` 
				(`admin_id`,`title`,`money`,`addtime`,`addmsg`,`dowhat`,`doconfig`,`havenum`) 
				VALUES 
				('".$admin['id']."','{$title}','{$money}','{$date}','{$addmsg}','{$dowhat}','','{$havenum}')
			");
			if($res)code(["code"=>"1","message"=>"添加成功！"]);
			else code(["code"=>"0","message"=>"添加失败！"]);
			
		}else code(["code"=>"0","message"=>"操你妈，老子有设置这种参数吗"]);
		
	}

	
	else if($type=='select'){
		$goodsid=$_POST["goodsid"];
		//订单编辑
		$res = $sql("SELECT * FROM pay_goods WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"抱歉了您勒，查无结果"]);
		$doconfig=json_decode($res['doconfig'],TRUE);
		if($res['zt']=='1')$zt="下架";
		else $zt='上架';
		
		if($res['dowhat']=='用户系统购买VIP'){
			code(["code"=>"1","message"=>"获取成功！",'model'=>[
				'sel_zt'=>$zt,
				'sel_dowhat'=>$res['dowhat'],
				'sel_title'=>$res['title'],
				'sel_money'=>$res['money'],
				'sel_addmsg'=>$res['addmsg'],
				'sel_havenum'=>$res['havenum'],
				'sel_sell'=>$res['sell'],
				
				'sel_czvip'=>$doconfig['money'],
			]]);
		}else if($res['dowhat']=='用户系统充值余额'){
			code(["code"=>"1","message"=>"获取成功！",'model'=>[
				'sel_zt'=>$zt,
				'sel_dowhat'=>$res['dowhat'],
				'sel_title'=>$res['title'],
				'sel_money'=>$res['money'],
				'sel_addmsg'=>$res['addmsg'],
				'sel_havenum'=>$res['havenum'],
				'sel_sell'=>$res['sell'],
				
				'sel_czmoney'=>$doconfig['money'],
			]]);
		}else if($res['dowhat']=='亿乐社区'){
			code(["code"=>"1","message"=>"获取成功！",'model'=>[
				'sel_zt'=>$zt,
				'sel_dowhat'=>$res['dowhat'],
				'sel_title'=>$res['title'],
				'sel_money'=>$res['money'],
				'sel_addmsg'=>$res['addmsg'],
				'sel_havenum'=>$res['havenum'],
				'sel_sell'=>$res['sell'],
				
				'sel_ylurl'=>$doconfig['api'],
				'sel_yltkid'=>$doconfig['tokenid'],
				'sel_ylkey'=>$doconfig['key'],
				'sel_ylgid'=>$doconfig['gid'],
				'sel_ylnum'=>$doconfig['num'],
			]]);
		}else if($res['dowhat']=='POST对接'){
			code(["code"=>"1","message"=>"获取成功！",'model'=>[
				'sel_zt'=>$zt,
				'sel_dowhat'=>$res['dowhat'],
				'sel_title'=>$res['title'],
				'sel_money'=>$res['money'],
				'sel_addmsg'=>$res['addmsg'],
				'sel_havenum'=>$res['havenum'],
				'sel_sell'=>$res['sell'],
				
				'sel_geturl'=>$doconfig['get'],
				'sel_getpost'=>$doconfig['post'],
				'sel_getcookie'=>$doconfig['cookie'],
			]]);
		}else if($res['dowhat']=='自动发货'){
			code(["code"=>"1","message"=>"获取成功！",'model'=>[
				'sel_zt'=>$zt,
				'sel_dowhat'=>$res['dowhat'],
				'sel_title'=>$res['title'],
				'sel_money'=>$res['money'], 
				'sel_addmsg'=>$res['addmsg'],
				'sel_havenum'=>$sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='".$res['id']."' "),
				'sel_sell'=>$res['sell'],
				
			]]);
		}
	} 
	
	
	
	
	else if($type=='sel_edu'){
		$goodsid = $_POST["sel_goodsid"];
		$res = $sql("SELECT * FROM pay_goods WHERE id='{$goodsid}' AND admin_id='".$admin['id']."'");
		if(!$res)code(["code"=>"0","message"=>"你他妈有权限嘛，滚一边去"]);
		
		$title = $_POST['sel_title'];
		$money = $_POST['sel_money'];
		$havenum = (int)$_POST['sel_havenum'];
		$sell=(int)$_POST["sel_sell"];
		$addmsg = $_POST['sel_addmsg'];
		$zt=$_POST['sel_zt'];
		if($zt=='上架')$zt='0';
		else if($zt=='下架')$zt='1';
		else code(["code"=>"0","message"=>"请选择商品状态"]);
		if($sell>99999999)code(["code"=>"0","message"=>"已出售的数量请勿设置太大"]);
		if(mb_strlen($title,'utf-8')>10 || mb_strlen($title,'utf-8')<2)code(["code"=>"0","message"=>"商品标题为2-10个字符"]);
		if(!is_money($money))code(["code"=>"0","message"=>"金额错误，9999.99-0.01"]);
		if($havenum>99999999)code(["code"=>"0","message"=>"商品库存须在1-99999999之间"]);
		if(mb_strlen($addmsg,'utf-8')>200)code(["code"=>"0","message"=>"商品介绍不得超过200字"]);
		
		
		$dowhat = $_POST['sel_dowhat'];
		
		if($dowhat=='POST对接'){
			$get = $_POST['sel_geturl'];
			$post = $_POST['sel_getpost'];
			$cookie = $_POST['sel_getcookie'];
			if(!$get)code(["code"=>"0","message"=>"必须配置url参数"]);
			if(mb_strlen($get,'utf-8')>300 || mb_strlen($get,'utf-8')<10)code(["code"=>"0","message"=>"请输入正确的url,10-100字符"]);
			if(mb_strlen($post,'utf-8')>300)code(["code"=>"0","message"=>"post内容需小于300字符"]);
			if(mb_strlen($cookie,'utf-8')>300)code(["code"=>"0","message"=>"cookie内容需小于300字符"]);
			if(sj($get,"http://","/")=='' && sj($get,'https://','/')=='')code(["code"=>"0","message"=>"网站需填写协议，http或https"]);
			
			$arr =['get'=>$get,'post'=>$post,'cookie'=>$cookie];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("UPDATE  `pay_goods` 
				SET title='{$title}',money='{$money}',addmsg='{$addmsg}',dowhat='{$dowhat}',doconfig='{$doconfig}',havenum='{$havenum}',sell='{$sell}',zt='{$zt}'
				WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' 
			");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
			
		}else if($dowhat=='亿乐社区'){
			$ylurl = $_POST["sel_ylurl"];
			$yltkid = (int)$_POST["sel_yltkid"];
			$ylkey = $_POST["sel_ylkey"];
			$ylgid = (int)$_POST["sel_ylgid"];
			$ylnum = $_POST["sel_ylnum"];
			
			if(mb_strlen($ylurl,'utf-8')<5 || mb_strlen($get,'utf-8')>100)code(["code"=>"0","message"=>"请输入正确的亿乐域名"]);
			if(is_set($ylurl,'/'))code(["code"=>"0","message"=>"亿乐域名勿填http://及/",]);
			if($yltkid<1)code(["code"=>"0","message"=>"请输入正确的tokenid"]);
			if(mb_strlen($ylkey,'utf-8')!=32)code(["code"=>"0","message"=>"请输入正确的32位key"]);
			if($ylgid<1)code(["code"=>"0","message"=>"商品ID不正确"]);
			if(!is_money($ylnum))code(["code"=>"0","message"=>"下单基数数值错误！"]);
			
			$arr =['api'=>$ylurl,'tokenid'=>$yltkid,'key'=>$ylkey,'gid'=>$ylgid,'num'=>$ylnum];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("UPDATE  `pay_goods` 
				SET title='{$title}',money='{$money}',addmsg='{$addmsg}',dowhat='{$dowhat}',doconfig='{$doconfig}',havenum='{$havenum}',sell='{$sell}',zt='{$zt}'
				WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' 
			");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
			
			
		}else if($dowhat=='用户系统充值余额'){
			$addmoney=$_POST['sel_czmoney'];
			if(!is_money($addmoney))code(["code"=>"0","message"=>"请输入正确的金额比例"]);
			
			$arr =['money'=>$addmoney];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("UPDATE  `pay_goods` 
				SET title='{$title}',money='{$money}',addmsg='{$addmsg}',dowhat='{$dowhat}',doconfig='{$doconfig}',havenum='{$havenum}',sell='{$sell}',zt='{$zt}'
				WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' 
			");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
			
		}else if($dowhat=='用户系统购买VIP'){
			
			
			$addvip=$_POST['sel_czvip'];
			
			
			if(!is_money($addvip))code(["code"=>"0","message"=>"请输入正确的VIP价格"]);
			
			$arr =['money'=>$addvip];
			$doconfig = json_encode($arr,JSON_UNESCAPED_UNICODE);
			
			$res = $sql("UPDATE  `pay_goods` 
				SET title='{$title}',money='{$money}',addmsg='{$addmsg}',dowhat='{$dowhat}',doconfig='{$doconfig}',havenum='{$havenum}',sell='{$sell}',zt='{$zt}'
				WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' 
			");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
			
		}else if($dowhat=='自动发货'){
			
			$res = $sql("UPDATE  `pay_goods` 
				SET title='{$title}',money='{$money}',addmsg='{$addmsg}',zt='{$zt}'
				WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' 
			");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
			
		}else code(["code"=>"0","message"=>"操你妈，老子有设置这种参数吗"]);
		
		
	}
