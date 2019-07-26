<?php
	// 签名顺序 => key.paytype.orderid.paymsg.goodsid.goodsval.token
	include("../function.php");
	$admins = $admin;
	if($_GET['type']=='demo' && $_GET['paytype']=='epay'){
		if(!$admin)code(["code"=>"-1","message"=>"登陆状态失效"]);
		$_REQUEST["key"]=$admin['apikey'];
		$_REQUEST["money"]='0.01';
		$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
		if(!$config)code(["code"=>"0","message"=>"管理员还未开启支付系统"]);
		if($config['qqpay']=='epay')$_REQUEST['paytype']='qqpay';
		else if($config['alipay']=='epay')$_REQUEST['paytype']='alipay';
		else if($config['wxpay']=='epay')$_REQUEST['paytype']='wxpay';
		else code(["code"=>"0","message"=>"您还没有设置易支付收款(返回后台->支付管理->接口配置->页面底部选择一种支付方式为易支付)"]);
	}else if($_GET['type']=='demo' && $_GET['paytype']=='mpay'){
		if(!$admin)code(["code"=>"-1","message"=>"登陆状态失效"]);
		$_REQUEST["key"]=$admin['apikey'];
		$_REQUEST["money"]='0.01';
		$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
		if(!$config)code(["code"=>"0","message"=>"管理员还未开启支付系统"]);
		if($config['qqpay']=='mpay')$_REQUEST['paytype']='qqpay';
		else if($config['alipay']=='mpay')$_REQUEST['paytype']='alipay';
		else if($config['wxpay']=='mpay')$_REQUEST['paytype']='wxpay';
		else code(["code"=>"0","message"=>"您还没有设置码支付收款(返回后台->支付管理->接口配置->页面底部选择一种支付方式为码支付)"]);
	}else if($_GET['type']=='cc'){
		// CC防刷单 验证码校验
		if($_SESSION["code"]!=$_GET['randcode'] || $_SESSION["code"]=='' || $_GET['randcode']=='')code(["code"=>"0","message"=>"图形验证码输入错误！"]);
		if($_SESSION['code_i']!='1')code(["code"=>"0","message"=>"图形验证码已过期"]);
		$_SESSION['code_i']=$_SESSION['code_i']+1;
		$_SESSION['pay_cc']="关闭";
		code(["code"=>"1","message"=>"yes"]);
	}
	
	
	
	$key = $_REQUEST["key"];
	$money = $_REQUEST["money"];		// 金额
	$paytype = $_REQUEST['paytype'];	// 支付方式，qqpay,alipay,wxpay
	$orderid = $_REQUEST['orderid'];	// 订单号
	$paymsg = $_REQUEST['paymsg'];		// 订单备注
	
	$goodsid = (int)$_REQUEST['goodsid'];					// 商品ID
	$goodsval = json_decode($_REQUEST['goodsval'],TRUE);	// 商品传值
	$token = $_REQUEST['token'];

	if($key=='')code(["code"=>"0","message"=>"缺少key的值"]);
	
	if($key == "admin_pay"){
		$admin = $sql("SELECT * FROM admin WHERE id='".$sup_admin."' ");
		if(!$admin['id'])code(["code"=>"0","message"=>"未配置后台支付接口，请打开/lib/config.php 配置支付接口管理员ID -> sup_admin"]);
	}else{
		$admin = $sql("SELECT * FROM admin WHERE apikey='".$key."' ");
		if(!$admin)code(["code"=>"0","message"=>"key错误！".$key]);
	}

	
	
	if(mb_strlen($_REQUEST['goodsval'],'utf-8')>3000)code(["code"=>"0","message"=>"商品参数最多传3000字符"]);
	if(!is_money($money) && !$goodsid)code(["code"=>"0","message"=>"支付金额错误！"]);
	if($paytype=='qqpay' || $paytype=='wxpay' || $paytype=='alipay'){}else code(["code"=>"0","message"=>"支付方式选择错误"]);
	if(!$orderid || $orderid=='')$orderid = strtotime("now").rand("1000","9999");
	if(mb_strlen($orderid,'utf-8') > 30)code(["code"=>"0","message"=>"订单号勿超过30字符"]);
	if($sql("SELECT * FROM pay_order WHERE admin_id='".$admin['id']."' AND orderid='{$orderid}' "))code(["code"=>"0","message"=>"该订单号已存在"]);
	if(mb_strlen($paymsg,'utf-8') > 300)code(["code"=>"0","message"=>"订单备注请勿太长"]);
	
	$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
	if(!$config)code(["code"=>"0","message"=>"管理员还未开启支付系统"]);
	
	
	
	
	
	// 判断支付动作
	if($goodsid>0){
		$goods = $sql("SELECT * FROM pay_goods WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' ");
		if(!$goods)code(["code"=>"0","message"=>"该商品ID不存在哦~"]);
		if($goods['zt']=='1')code(["code"=>"0","message"=>"该商品已下架"]);
		if($goods['havenum']<1 && $goods['dowhat']!='自动发货')code(["code"=>"0","message"=>"商品库存不足"]);
		
		if($token!=md5($admin['apikey'].$paytype.$orderid.$paymsg.$goodsid.$_REQUEST['goodsval'].$admin['token']))code(["code"=>"0","message"=>"Token校验失败！"]);
		
		if($goods['dowhat']=='POST对接'){
			// 商品传值 ： {'key':'val','key2':'val2'}
			foreach( $goodsval as  $key => $val ){
				$goods['doconfig'] = str_replace("{{".$key."}}",$val,$goods['doconfig']);
			}
			$goodsval = $goods['doconfig'];
			$money = $goods['money'];
			
			
			
			
		}else if($goods['dowhat']=='亿乐社区'){
			
			$yl_num = (int)$goodsval['num'];
			$yl_value1 = $goodsval['value1'];
			$yl_value2 = $goodsval['value2'];
			$yl_value3 = $goodsval['value3'];
			$yl_value4 = $goodsval['value4'];
			$yl_value5 = $goodsval['value5'];
			$yl_value6 = $goodsval['value6'];
			if($yl_num<1 || $yl_num >9999999999)code(["code"=>"0","message"=>"下单数量错误！"]);
			if(!$yl_value1)code(["code"=>"0","message"=>"请提交参数1"]);
			$goodsval = json_encode(['num'=>$yl_num,'value1'=>$yl_value1,'value2'=>$yl_value2,'value3'=>$yl_value3,'value4'=>$yl_value4,'value5'=>$yl_value5,'value6'=>$yl_value6],JSON_UNESCAPED_UNICODE);
			
			$money = $goods['money']*$yl_num;
			
			
			
		}else if($goods['dowhat']=='用户系统充值余额'){
			$money = $goodsval['money'];
			$user = $goodsval['user'];
			if(!is_money($money))code(["code"=>"0","message"=>"请输入正确的充值金额，0.01到9999.99之间"]);
			$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
			if(!$res)code(["code"=>"0","message"=>"该账号不存在"]);
			$goodsval = json_encode(['user'=>$user,'money'=>$money],JSON_UNESCAPED_UNICODE);
			
			$money = $money;
			
		}else if($goods['dowhat']=='用户系统购买VIP'){
			$user = $goodsval['user'];
			$num = (int)$goodsval['num'];
			if($num<1 || $num>999)code(["code"=>"0","message"=>"购买VIP月份数量错误！"]);
			$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
			if(!$res)code(["code"=>"0","message"=>"该账号不存在"]);
			$goodsval = json_encode(['user'=>$user,'num'=>$num],JSON_UNESCAPED_UNICODE);
			
			$money = json_decode($goods['doconfig'],TRUE)['money']*$num;
			
		}else if($goods['dowhat']=='自动发货'){
			
			$mail = $goodsval['mail'];
			$num = (int)$goodsval['num'];
			if($num>100)code(["code"=>"0","message"=>"单次请勿下单100份以上"]);
			if(!$num || $num<1)$num=1;
			if($sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='".$goodsid."' AND issell='0' ")<$num)code(["code"=>"0","message"=>"库存不足(".$num.")了哦"]);
			$goodsval = json_encode(['mail'=>$mail,'num'=>$num],JSON_UNESCAPED_UNICODE);
			$money = $goods['money']*$num;
			
		}
		
		
	}else{
		
		
		if($token!=md5($admin['apikey'].$paytype.$orderid.$paymsg.$money.$admin['token']) && $_REQUEST['orderid']>0){
			if($_GET['type']=="demo"){
				if(!$admin)code(["code"=>"0","message"=>"Token校验失败！-01"]);
			}else code(["code"=>"0","message"=>"Token校验失败！1"]);
		}else if(!$_REQUEST['orderid'] && !$admins){
			
			if($token!=md5($admin['apikey'].$paytype.$paymsg.$money.$admin['token']))code(["code"=>"0","message"=>"签名校验失败！-002"]);
			
		}else if($_GET['type']=='demo' && ($_GET['paytype']=='epay' || $_GET['paytype']=='mpay') && $admins){
			
		}else if($admin['id']==$sup_admin){
			
		}else{
			 
			if($token!=md5($admin['apikey'].$paytype.$orderid.$paymsg.$money.$admin['token']))code(["code"=>"0","message"=>"Token校验失败！-005"]);
			 
		}
		
		
		$goodsval='';
	}
	
	$needmoney = $money * $pay_need;
	if($admin['money']<$needmoney && !$admins)code(["code"=>"0","message"=>"管理员余额不足，请联系软件管理员处理"]);
	
	
	
	// 选择支付通道
	if($paytype=='qqpay'){
		if($config['qqpay']=='off' || $config['qqpay']=='')code(["code"=>"0","message"=>"管理员已关闭QQ支付通道"]);
		if($config['qqpay']=='epay'){
			$payon='epay';
			if($config['e_pid']=='' || $config['e_key']=='' || $config['e_api']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}else if($config['qqpay']=='mpay'){
			$payon='mpay';
			if($config['m_pid']=='' || $config['m_key']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}
	}else if($paytype=='wxpay'){
		if($config['wxpay']=='off' || $config['wxpay']=='')code(["code"=>"0","message"=>"管理员已关闭微信支付通道"]);
		if($config['wxpay']=='epay'){
			$payon='epay';
			if($config['e_pid']=='' || $config['e_key']=='' || $config['e_api']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}else if($config['wxpay']=='mpay'){
			$payon='mpay';
			if($config['m_pid']=='' || $config['m_key']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}
	}else{
		if($config['alipay']=='off' || $config['alipay']=='')code(["code"=>"0","message"=>"管理员已关闭支付宝支付通道"]);
		if($config['alipay']=='epay'){
			$payon='epay';
			if($config['e_pid']=='' || $config['e_key']=='' || $config['e_api']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}else if($config['alipay']=='mpay'){
			$payon='mpay';
			if($config['m_pid']=='' || $config['m_key']=='')code(["code"=>"0","message"=>"管理员未配置支付接口"]);
		}
	}
	
	
	
	if($sql("SELECT count(*) FROM pay_order WHERE admin_id='".$admin['id']."' AND TO_DAYS(add_time)=TO_DAYS(NOW()) ") > 200){
		// 全部判断结束后开始 防刷判断
		if(!$_SESSION['pay_cc'])$_SESSION['pay_cc']='开启';
		if($_SESSION['pay_cc']=="开启"){
			cc($orderid, $money, $paytype);
			die();
		}else{
			$_SESSION['pay_cc']="开启";
		}
	}
	
	if($paymsg=="账户充值" && $admins){
		$iscz='1';
		$paymsg="账户充值(".$admins['user'].")";
	}else{
		$iscz='0';
	}
	
	
	
	$return_url = $t1zf.'rt_'.$config['return_mb'].'.html?key='.$admin['apikey'];
	if($payon=='epay'){
		$res = $sql("INSERT INTO `pay_order` 
			(`admin_id`,`orderid`,`goodsid`,`goodsval`,`paytype`,`paysys`,`money`,`add_time`,`add_ip`,`add_msg`,`iscz`) 
				VALUES 
			('".$admin['id']."','{$orderid}','{$goodsid}','{$goodsval}','{$paytype}','epay','{$money}','{$date}','".ip()."','{$paymsg}','{$iscz}')
		");
		if(!$res)code(["code"=>"0","message"=>"订单创建失败！"]);
			
		epay($config['e_api'], $config['e_pid'], $config['e_key'], $paytype, $money, $orderid, $return_url, $t1zf.'epay.notify?key='.$admin['apikey'],'');
			
	}else if($payon=='mpay'){
		$res = $sql("INSERT INTO `pay_order` 
			(`admin_id`,`orderid`,`goodsid`,`goodsval`,`paytype`,`paysys`,`money`,`add_time`,`add_ip`,`add_msg`,`iscz`) 
				VALUES 
			('".$admin['id']."','{$orderid}','{$goodsid}','{$goodsval}','{$paytype}','mpay','{$money}','{$date}','".ip()."','{$paymsg}','{$iscz}')
		");
		if(!$res)code(["code"=>"0","message"=>"订单创建失败！"]);
			
		mpay($config['m_pid'],$config['m_key'],$paytype,$money,$orderid,$return_url,$t1zf.'mpay.notify?key='.$admin['apikey'],'');
			
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function mpay($m_pid,$m_key,$paytype,$money,$orderid,$rt,$nt,$srcurl=''){
		$codepay_config['return_url'] = $rt;
		$codepay_config['notify_url'] = $nt;
		error_reporting(E_ALL & ~E_NOTICE);
		date_default_timezone_set('PRC');
		$codepay_config['id'] = $m_pid;
		$codepay_config['key'] = $m_key;
		$codepay_config['act'] = '0';
		$codepay_config['page'] = 4;
		$codepay_config['style'] = 1;
		$codepay_config['outTime'] = 600;
		$codepay_config['min'] = 0.01;
		$codepay_config['pay_type'] = 1;
		$codepay_config['user'] = 'admin';
		$codepay_config['userOff'] = false;
		define('HTTPS', false);
		$codepay_config['gateway'] = '';
		$codepay_config['go_time'] = 3;
		$codepay_config['go_url'] = $codepay_config['return_url']; 
		define('ROOT_PATH', dirname(__FILE__)); //这是程序目录常量
		define('DEBUG', true);  //调试模式启用
		define('LOG_PATH', ROOT_PATH . '/log.txt');
		define('DB_PREFIX', 'codepay');
		if($paytype=='alipay')$type='1';
		else if($paytype=='qqpay')$type='2';
		else $type='3';
		$parameter = array(
		    "id" => (int)$codepay_config['id'],//平台ID号
		    "type" => $type,//支付方式
		    "price" => (float)$money,//原价
		    "pay_id" => $orderid, //可以是用户ID,站内商户订单号,用户名
		    "param" => $param,//自定义参数
		    "act" => (int)$codepay_config['act'],//此参数即将弃用
		    "outTime" => (int)$codepay_config['outTime'],//二维码超时设置
		    "page" => (int)$codepay_config['page'],//订单创建返回JS 或者JSON
		    "return_url" => $codepay_config["return_url"],//付款后附带加密参数跳转到该页面
		    "notify_url" => $codepay_config["notify_url"],//付款后通知该页面处理业务
		    "style" => (int)$codepay_config['style'],//付款页面风格
		    "pay_type" => $codepay_config['pay_type'],//支付宝使用官方接口
		    "user_ip" => ip(),//付款人IP
		    "qrcode_url" => $codepay_config['qrcode_url'],//本地化二维码
		    "chart" => trim(strtolower($codepay_config['chart']))//字符编码方式
		);
		$back = create_link($parameter, $codepay_config['key']);
		switch ((int)$type) {
		    case 1:
		        $typeName = '支付宝';
		        break;
		    case 2:
		        $typeName = 'QQ';
		        break;
		    default:
		        $typeName = '微信';
		}
		$user_data = array("return_url" => $parameter["return_url"],"type" => $parameter['type'], "outTime" => $parameter["outTime"], "codePay_id" => $parameter["id"], "logoShowTime" => 2);
		$user_data["qrcode_url"] = $codepay_config["qrcode_url"];
		$user_data["logoShowTime"] = 2;
		if ($parameter['page'] != 3) { //只要不为3 返回JS 就去服务器加载资源
		    $parameter['page'] = "4"; //设置返回JSON
		    $back = create_link($parameter, $codepay_config['key'],$codepay_config['gateway']); //生成支付URL
		    if (function_exists('file_get_contents')) { //如果开启了获取远程HTML函数 file_get_contents
		        $codepay_json = file_get_contents($back['url']); //获取远程HTML
		    } else if (function_exists('curl_init')) {
		        $ch = curl_init(); //使用curl请求
		        $timeout = 5;
		        curl_setopt($ch, CURLOPT_URL, $back['url']);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		        $codepay_json = curl_exec($ch);
		        curl_close($ch);
		    }
		}
	
		if (empty($codepay_json)) { //如果没有获取到远程HTML 则走JS创建订单
		    $parameter['call'] = "callback";
		    $parameter['page'] = "3";
		    $back = create_link($parameter, $codepay_config['key'],'https://codepay.fateqq.com/creat_order/?');
		    $codepay_html = '<script src="' . $back['url'] . '"></script>'; //JS数据
		} else { //获取到了JSON
		    $codepay_data = json_decode($codepay_json);
		    $qr = $codepay_data ? $codepay_data->qrcode : '';
		    $codepay_html = "<script>callback({$codepay_json})</script>"; //JSON数据
		}
	
		echo '<!DOCTYPE html>
			<html>
			<head>
			    <meta http-equiv="Content-Type" content="text/html; charset='.$codepay_config['chart'].'">
			    <meta http-equiv="Content-Language" content="zh-cn">
			    <meta name="apple-mobile-web-app-capable" content="no"/>
			    <meta name="apple-touch-fullscreen" content="yes"/>
			    <meta name="format-detection" content="telephone=no,email=no"/>
			    <meta name="apple-mobile-web-app-status-bar-style" content="white">
			    <meta name="viewport"content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
			    <title>'.$typeName.' - 扫码支付</title>
			    <link href="src/css/wechat_pay.css" rel="stylesheet" media="screen">
			
			</head>
			
			<body>
			<div class="body">
			    <h1 class="mod-title">
			        <span class="ico_log ico-'.$type.'"></span>
			    </h1>
			
			    <div class="mod-ct">
			        <div class="order">
			        </div>
			        <div class="amount" id="money">￥'.$price.'</div>
			        <div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
			            <div data-role="qrPayImg" class="qrcode-img-area">
			                <div class="ui-loading qrcode-loading" data-role="qrPayImgLoading" style="display: none;">加载中</div>
			                <div style="position: relative;display: inline-block;">
			                    <img id="show_qrcode" alt="加载中..." src="'.$qr.'" width="210" height="210" style="display: block;">
			                    <img onclick="$("#use").hide()" id="use"
			                         src="src/img/use_'.$type.'.png"
			                         style="position: absolute;top: 50%;left: 50%;width:32px;height:32px;margin-left: -21px;margin-top: -21px">
			                </div>
			            </div>
			
			
			        </div>
			        
			        <div class="time-item" id="msg">
			            <h1>二维码过期时间</h1>
			            <strong id="hour_show">0时</strong>
			            <strong id="minute_show">0分</strong>
			            <strong id="second_show">0秒</strong>
			        </div>
			
			        <div class="tip">
			            <div class="ico-scan"></div>
			            <div class="tip-text">
			                <p>请使用'.$typeName.' 扫一扫</p>
			                <p>扫描二维码完成支付</p>
			            </div>
			        </div>
			
			        <div class="detail" id="orderDetail">
			            <dl class="detail-ct" id="desc" style="display: none;">
			
			                <dt>状态</dt>
			                <dd id="createTime">订单创建</dd>
			
			            </dl>
			            <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
			        </div>
			
			        <div class="tip-text">
			        </div>
			
			
			    </div>
			    <div class="foot">
			        <div class="inner">
			            <p>手机用户可保存上方二维码到手机中</p>
			            <p>在'.$typeName.'扫一扫中选择“相册”即可</p>
			        </div>
			    </div>
			
			</div>
			
			
			<!--注意下面加载顺序 顺序错乱会影响业务-->
			<script src="src/js/jquery-1.10.2.min.js"></script>
			<!--[if lt IE 8]>
			<script src="src/js/json3.min.js"></script><![endif]-->
			<script>
			    var user_data ='.json_encode($user_data).'
			</script>
			<script src="src/js/notify.js"></script>
			<script src="src/js/codepay_util.js"></script>
			'.$codepay_html.'
			<script>
			    setTimeout(function () {
			        $("#use").hide() //2秒后隐藏中间那LOGO
			    }, user_data.logoShowTime || 2000);
			</script>
			</body>
			</html>
		';
	}
	
	
	
	// 易支付，支付接口
	function epay($e_api,$e_pid,$e_key,$paytype,$money,$orderid,$return_url,$notify_url){
		require_once("lib/epay_submit.class.php");
		$alipay_config['partner']		= $e_pid;
		$alipay_config['key']			= $e_key;
		$alipay_config['apiurl']    = $e_api;
		if(sj($e_api,'http','://')=="s")$alipay_config['transport']='https';
		else $alipay_config['transport']='http';
		$alipay_config['sign_type']    = strtoupper('MD5');
		$alipay_config['input_charset']= strtolower('utf-8');
		$parameter = array(
			"pid" => trim($alipay_config['partner']),
			"type" => $paytype,
			"notify_url"	=> $notify_url,
			"return_url"	=> $return_url,
			"out_trade_no"	=> $orderid,
			"name"	=> "T1支付",
			"money"	=> $money,
			"sitename"	=> "T1支付"
		);
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter);
		echo $html_text;
	}

	
	
	function cc($orderid,$money,$paytype){
		if($paytype=='qqpay')$paytype='QQ钱包';
		else if($paytype=='wxpay')$paytype='微信支付';
		else $paytype='支付宝';
		echo '	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<title>支付订单确认</title>
						<link rel="stylesheet" type="text/css" href="admin/css/bootstrap.min.css"/>
						<link rel="stylesheet" type="text/css" href="src/css/pay_cc.css"/>
						<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" />
						<meta name="format-detection" content="telephone=no">
					</head>
					<body>
						
						<div>
							<h3>支付订单确认</h3>
							<div class="">
								
								<hr>
								
								<h4>
									单号：<b>'.$orderid.'</b>
									<br>金额：<b>￥'.$money.'</b>
									<br>付款：<b>'.$paytype.'</b>
									<br><br>请输入验证码：
								</h4>
								
								<div class="input-group" style="height: 40px;">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-picture" style="color: rgb(92, 205, 222);"></span>
									</div>
									<input maxlength="4" type="tel" placeholder="右侧图形数字验证码" class="form-control" style="height: 40px;">
									<div class="input-group-addon rand" style="height: 40px;width:60px;background-image: url(randcode.img);background-size: 100% 100%;"></div>
								</div>
								
								<div class="form-group" style="margin-top: 15px;">
									<div class="btn btn-primary btn-block">确认</div>
								</div>
								
							</div>
							
						</div>
						
						<script src="admin/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
						<script src="admin/js/plugins/layer/layer.min.js" type="text/javascript" charset="utf-8"></script>
						<script src="src/js/pay_cc.js" type="text/javascript" charset="utf-8"></script>
					</body>
				</html>
		';
	}
