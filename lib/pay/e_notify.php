<?php
	include '../function.php';
	require_once("lib/epay_notify.class.php");
	$key = $_GET["key"];
	$_GET['key']='';
	if($key=='')exit('fail');
	$orderid=$_GET['out_trade_no'];
	$admin = $sql("SELECT * FROM admin WHERE apikey='".$key."' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！"]);
	$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
	if(!$config)code(["code"=>"0","message"=>"管理员还未开启支付系统"]);
	$alipay_config['partner']		= $config['e_pid'];
	$alipay_config['key']			= $config['e_key'];
	$alipay_config['sign_type']    = strtoupper('MD5');
	$alipay_config['input_charset']= strtolower('utf-8');
	if(sj($config['e_api'],'http','://')=="s")$alipay_config['transport']='https';
	else $alipay_config['transport']='http';
	$alipay_config['apiurl']    =$config['e_api'];
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyNotify();
	if($verify_result) {
		if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
			
			$res = $sql("UPDATE  `pay_order` SET ispay='1',pay_time='{$date}' WHERE orderid='{$orderid}' AND admin_id='".$admin['id']."' ");
			if($res){
				if($config['ismail']==1){
					$res = $sql("INSERT INTO `pay_order_mail` (`admin_id`,`orderid`) VALUES ('".$admin['id']."','{$orderid}')");
				}
				echo "success";
			}
			else echo "no";
			
		}
	}
	else {
		echo "fail";
	}
?>