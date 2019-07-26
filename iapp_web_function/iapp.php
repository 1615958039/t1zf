<?php
	// 
	//  iapp.php
	//  iApp常用函数之php同名封装
	//  
	//  Created by 杰哥 on 2019-04-13.
	//  Copyright 2019 Jiege. All rights reserved.
	//  
	//  说明:
	//  建议 PHP版本>7.0
	//  
	//  
	//  
	//  
	
	error_reporting(0);	//关闭报错
	header("Content-Type: text/html;charset=utf-8");	//声明网站编码为utf-8
	date_default_timezone_set("Asia/Shanghai");	//设置脚本时区为上海
	
	function fd($path){
		//删除文件 -> fd("demo.txt");
		return unlink($path);
	}
	
	function fe($path){
		//判断文件是否存在 -> fe("demo.txt");
		return file_exists($path);
	}
	
	function fs($path){
		//获取文件大小 -> fs("demo.txt");	单位B
		return filesize($path);
	}
	
	function fr($path){
		//读取文件内容 -> fr("demo.txt");
		return file_get_contents($path);
	}
	
	function fc($path,$newpath,$iscover='true'){
		//复制文件 -> fc("demo.txt","demo.txt.bar");
		if($iscover==false)if(file_exists($newpath))return FALSE;
		return copy($path,$newpath);
	}
	
	function fw($path,$data){
		//写入文件 -> fw("demo.txt","这里是数据");
		return file_put_contents($path,$data)?TRUE:FALSE;
	}
	
	function fl($path){
		//获取目录 -> fl("./demo/");
		$arr = scandir($path);
		if(!$arr)return FALSE;
		unset($arr[0]);
		unset($arr[1]);
		return array_values($arr);
	}
	
	function sr($subject,$search,$replace){
		//替换字符 -> sr("卧槽你傻逼","呀","卧槽"); 函数返回替换后的字符串
		return str_replace($search, $replace, $subject);
	}
	
	function sj($text,$a,$b){
		//截取字符 -> sj("这是一条字符串","条","串");
		preg_match_all('/'.$a.'(.*?)'.$b.'/si',$text,$rt);
		return $rt[1][0];
	}
	
	function hs($url,$postdata='',$cookie='',$header=[],$rtcookie=FALSE){
		//获取网址源码 -> hs("https://www.baidu.com");
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postdata));
        }
        if($cookie)curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		if($header)curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_TIMEOUT,60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($curl);
        if (curl_errno($curl))return curl_error($curl);
        curl_close($curl);
        if($rtcookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0],1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
	}
	
	function stobm($text){
		//中文utf8字符转url编码 -> stobm("测试");
		return urlencode($text);
	}
	
	function sutf8to($text){
		//url编码转utf8中文 -> sutf8to("%E6%88%91%E6%97%A5");
		return urldecode($text);
	}
	
	function syso($text){
		echo "<script>console.log('".$text."')</script>";
	}
	
	
	function get_user_ip(){
		//获取用户端ip地址 -> ip();
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
	
	
	
	
	