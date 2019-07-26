<?php
	error_reporting(0);
	header("Content-Type: text/html;charset=utf-8");
	session_start();
	date_default_timezone_set("Asia/Shanghai");
	
	include("config.php");
	
	
	
	
	
		
	
	
	
	
	$dbconnect  =  mysqli_connect($loca,$user,$pass,$name);
	if(!$dbconnect)die('服务器链接失败~');
	mysqli_set_charset($dbconnect,'UTF8');
	$sql = function($sqlstatement,$type='')use($dbconnect){
		if(strpos($sqlstatement,"count(*)")>0){
			$result = mysqli_query($dbconnect, $sqlstatement);
			$row = mysqli_fetch_assoc($result);
			return $row['count(*)'];
		}else if(strpos($sqlstatement,"select")===0 || strpos($sqlstatement,"SELECT")===0){
			$result = mysqli_query($dbconnect, $sqlstatement);
			if($type=="list"){
				$i=0;$arr=array();
				while($row = mysqli_fetch_assoc($result))$arr[$i++] = $row;
				return $arr;
			}
			return mysqli_fetch_assoc($result);
		}else if(strpos($sqlstatement,"UPDATE")===0 || strpos($sqlstatement,"update")===0 ||
				 strpos($sqlstatement,"INSERT")===0 || strpos($sqlstatement,"insert")===0 ||
				 strpos($sqlstatement,"DELETE")===0 || strpos($sqlstatement,"delete")===0){
			if($type=="get_last_id" && (strpos($sqlstatement,"INSERT")===0 || strpos($sqlstatement,"insert")===0)){
				$result = mysqli_query($dbconnect, $sqlstatement);
				if($result){
					return mysqli_insert_id($dbconnect);
				}else{
					return false;
				}
			}else{
				return mysqli_query($dbconnect, $sqlstatement);
			}
		}else{
			$result = mysqli_query($dbconnect, $sqlstatement);
			return mysqli_fetch_assoc($result);
		}
	};
	
	function ip(){
	    $realip = '';
	    if (isset($_SERVER)) {
	        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
	            foreach ($arr as $ip) {
	                $ip = trim($ip);
	                if ($ip != 'unknown') {
	                    $realip = $ip;
	                    break;
	                }
	            }
	        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	            $realip = $_SERVER['HTTP_CLIENT_IP'];
	        } else {
	            if (isset($_SERVER['REMOTE_ADDR'])) {
	                $realip = $_SERVER['REMOTE_ADDR'];
	            } else {
	                $realip = '0.0.0.0';
	            }
	        }
	    }
	    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
	    return $realip;
	}
	
	
	// 跳转页面
	function uigo($page,$alert=''){
		if($alert){
			echo "<script>alert('".$alert."');location.href='".$page."'</script>";
			die('');
		}else{
			echo "<script>location.href='".$page."'</script>";
			die('');
		}
	}
	
	
	// 爬虫接口
	function hs($url,$post='',$cookie='',$type='',$httpheader='',$returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.26 Safari/537.36 Core/1.63.6788.400 QQBrowser/10.3.2767.400');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, $url);
		if($type=="put"){
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "put");
		}
		if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie){
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
		if($httpheader){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
		}
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
	}
	
	// 截取数据
	function sj($text,$a,$b){
		if(strpos($a,'<')!==false)$a=str_replace("<","\<",$a);
		if(strpos($a,'>')!==false)$a=str_replace(">","\>",$a);
		if(strpos($a,'/')!==false)$a=str_replace("/","\/",$a);
		if(strpos($b,'<')!==false)$b=str_replace("<","\<",$b);
		if(strpos($b,'>')!==false)$b=str_replace(">","\>",$b);
		if(strpos($b,'/')!==false)$b=str_replace("/","\/",$b);
		preg_match_all('/'.$a.'(.*?)'.$b.'/si',$text,$cnm2);
		return @$cnm2[1][0];
	}
	
	// json返回数据
	function code($arr){
		die(json_encode($arr,JSON_UNESCAPED_UNICODE));
	}
	
	// 判断是否为金额 0.01 - 9999.99
	function is_money($money){
		if(mb_strlen($money,'utf-8') > 7 || mb_strlen($money,'utf-8') < 1)return FALSE;
		$money = str_replace(".","点",$money);
		if(is_set($money,'点')){
			$zs = sj("mmp".$money,"mmp","点");
			if($zs=='')return FALSE;
			if($zs > 9999 || $zs < 0)return FALSE;
			$xs = '1'.sj($money."mmp","点","mmp");
			if($xs > 199 || $xs < 10)return FALSE;
		}else{
			if($money > 9999 || $money <0)return FALSE;
		}
		if($money == "0点0" || $money =="0点00" || $money == "0")return FALSE;
		return TRUE;
	}
	
	
	
	// 云片 短信接口 => 手机号，随机验证码     充值地址 www.yunpian.com
	function sms($user,$randcode){
		global $sms_key;
		global $sms_ts;
		global $sms_te;
		$code = hs("https://sms.yunpian.com/v2/sms/single_send.json",array('apikey'=>$sms_key,'mobile'=>$user,'text'=>$sms_ts.$randcode.$sms_te),"","",array('Accept:application/json;charset=utf-8;','Content-Type:application/x-www-form-urlencoded;charset=utf-8;'));
		if(!$code)return FALSE;
		$code = json_decode($code, true);
		if($code['code']==0)return TRUE;
		else return FALSE;
	}
	
	
	//判断客户端是否是手机
	function is_mobile(){
		if(strpos($_SERVER['HTTP_USER_AGENT'],'Mobile')!==false)return true;
		return false;
	}
	
	//判断是否存在某个字符串
	function is_set($text,$keyword){
		if(strpos($text,$keyword)!==false)return TRUE;
		else return FALSE;
	}
	
	//人数变 k,w,
	function usersnum($num){
		if($num<100){
			return $num;
		}else if($num>=100 && $num<1000){
			return round(($num/1000),1)."k";
		}else if($num>=1000 && $num<9999){
			return round(($num/1000),1)."k";
		}else{
			return round(($num/10000),1)."w";
		}
	}
	
	
	
	// 管理员ID加密 => ID,方式  (不填方式默认为加密)
	function admin_id($id,$type=''){
		if($type){
			$id = str_replace("r","0",$id);
			$id = str_replace("q","1",$id);
			$id = str_replace("i","2",$id);
			$id = str_replace("w","3",$id);
			$id = str_replace("t","4",$id);
			$id = str_replace("y","5",$id);
			$id = str_replace("u","6",$id);
			$id = str_replace("e","7",$id);
			$id = str_replace("a","8",$id);
			$id = str_replace("p","9",$id);
			return 90000000 - $id;
		}else{
			$id = 90000000 - $id;
			$id = str_replace("0","r",$id);
			$id = str_replace("1","q",$id);
			$id = str_replace("2","i",$id);
			$id = str_replace("3","w",$id);
			$id = str_replace("4","t",$id);
			$id = str_replace("5","y",$id);
			$id = str_replace("6","u",$id);
			$id = str_replace("7","e",$id);
			$id = str_replace("8","a",$id);
			$id = str_replace("9","p",$id);
			return $id;
		}
	}
	
	// 码支付签名计算函数
	function create_link($params, $codepay_key, $host = ""){
		ksort($params); //重新排序$data数组
		reset($params); //内部指针指向数组中的第一个元素
		$sign = '';
	    $urls = '';
	    foreach ($params AS $key => $val) {
	        if ($val == '') continue;
	        if ($key != 'sign') {
	            if ($sign != '') {
	                $sign .= "&";
	                $urls .= "&";
	            }
	            $sign .= "$key=$val"; //拼接为url参数形式
	            $urls .= "$key=" . urlencode($val); //拼接为url参数形式
	        }
	    }
	  	$key = md5($sign . $codepay_key);//开始加密
	    $query = $urls . '&sign=' . $key; //创建订单所需的参数
	    $apiHost = ($host ? $host : "http://api2.fateqq.com:52888/creat_order/?"); //网关
	    $url = $apiHost . $query; //生成的地址
	    return array("url" => $url, "query" => $query, "sign" => $sign, "param" => $urls);
	}
	
	//	int型 数字对称加密，传入数字
	function numcode($num){
		$a = $num * 2.222;
		$a = str_replace('1','q',$a);
		$a = str_replace('2','a',$a);
		$a = str_replace('3','h',$a);
		$a = str_replace('4','w',$a);
		$a = str_replace('5','p',$a);
		$a = str_replace('6','x',$a);
		$a = str_replace('7','e',$a);
		$a = str_replace('8','d',$a);
		$a = str_replace('9','c',$a);
		$a = str_replace('0','r',$a);
		$a = str_replace('.','f',$a);
		return $a;
	}
	//解密，传入加密值
	function numdecode($num){
		$a = $num;
		$a = str_replace('q','1',$a);
		$a = str_replace('a','2',$a);
		$a = str_replace('h','3',$a);
		$a = str_replace('w','4',$a);
		$a = str_replace('p','5',$a);
		$a = str_replace('x','6',$a);
		$a = str_replace('e','7',$a);
		$a = str_replace('d','8',$a);
		$a = str_replace('c','9',$a);
		$a = str_replace('r','0',$a);
		$a = str_replace('f','.',$a);
		$a = $a / 2.222;
		return $a;
	}
	
	
	//另一种数字加密，和上面差不多
	function keycode($num){
		$a = $num * 2.222;
		$a = str_replace('1','z',$a);
		$a = str_replace('2','x',$a);
		$a = str_replace('3','c',$a);
		$a = str_replace('4','v',$a);
		$a = str_replace('5','b',$a);
		$a = str_replace('6','n',$a);
		$a = str_replace('7','m',$a);
		$a = str_replace('8','a',$a);
		$a = str_replace('9','s',$a);
		$a = str_replace('0','d',$a);
		$a = str_replace('.','f',$a);
		return $a;
	}
	function keydecode($num){
		$a = $num;
		$a = str_replace('z','1',$a);
		$a = str_replace('x','2',$a);
		$a = str_replace('c','3',$a);
		$a = str_replace('v','4',$a);
		$a = str_replace('b','5',$a);
		$a = str_replace('n','6',$a);
		$a = str_replace('m','7',$a);
		$a = str_replace('a','8',$a);
		$a = str_replace('s','9',$a);
		$a = str_replace('d','0',$a);
		$a = str_replace('f','.',$a);
		$a = $a / 2.222;
		return $a;
	}
	
	//textlist数字加密，和上面差不多
	function listlistcode($num){
		$a = $num * 2.222;
		$a = str_replace('1','r',$a);
		$a = str_replace('2','t',$a);
		$a = str_replace('3','g',$a);
		$a = str_replace('4','a',$a);
		$a = str_replace('5','x',$a);
		$a = str_replace('6','m',$a);
		$a = str_replace('7','p',$a);
		$a = str_replace('8','q',$a);
		$a = str_replace('9','k',$a);
		$a = str_replace('0','z',$a);
		$a = str_replace('.','e',$a);
		return $a;
	}
	function textlistdecode($num){
		$a = $num;
		$a = str_replace('r','1',$a);
		$a = str_replace('t','2',$a);
		$a = str_replace('g','3',$a);
		$a = str_replace('a','4',$a);
		$a = str_replace('x','5',$a);
		$a = str_replace('m','6',$a);
		$a = str_replace('p','7',$a);
		$a = str_replace('q','8',$a);
		$a = str_replace('k','9',$a);
		$a = str_replace('z','0',$a);
		$a = str_replace('e','.',$a);
		$a = $a / 2.222;
		return $a;
	}
	
	
	
	//发件接口
	function smtp($smtp,$port,$isssl,$name,$user,$pass,$title,$text,$who){
		include("lib/class.phpmailer.php");
		include("lib/class.smtp.php");
		$mail=new PHPMailer();
		$mail->CharSet='UTF-8';
		$mail->Port = $port;
		if($isssl)$mail->SMTPSecure = 'ssl';
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = $smtp;
		$mail->Username = $user; 
		$mail->Password = $pass;
		$mail->From = $user;
		$mail->AddAddress($who,$title);
		$mail->Subject = "=?utf-8?B?".base64_encode($title)."?=";
		$mail->FromName = $name;
		$mail->MsgHTML($text);
		$mail->IsHTML(true);
		if(!$mail->Send()) return FALSE;
		else return TRUE;
	}
	
	
	
/* 
	小白看 -> 
	
	增一条数据
	$res = $sql("INSERT INTO `users` (`user`,`pass`,`type`) VALUES ('{$user}','{$pass}','{$type}')");
	查一条数据
	$res = $sql("SELECT * FROM users WHERE id='1' ");
	查多条数据
	$res = $sql("SELECT * FROM users ORDER BY id DESC","list");
	foreach($res as $val){
		echo $val['id'].'<br>';
	}
	改
	$res = $sql("UPDATE  `app_users` SET jf='$xjf' WHERE  allid='$allid'");
	删
	$res = $sql("DELETE FROM app_km WHERE km='$km' AND user='$adminuser' ");
	累计条数
	$sql("SELECT count(*) FROM app_users WHERE id='$user'");
 */