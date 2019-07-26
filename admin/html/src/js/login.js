window.onload = function(){
	function setCookie(name,value,Days){
		var exp = new Date();
		exp.setTime(exp.getTime() + Days*24*60*60*1000);
		document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
	}
	function getCookie(name){
		var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
		if(arr=document.cookie.match(reg))
			return unescape(arr[2]);
		else
			return null;
	}
	var vm = new Vue({
		el:"#app",
		data:{
			ui_type:1, // 1登陆 2注册 3找回
			
			login_user:'',
			login_pass:'',
			login_code:'',
			login_btn_text:'登陆',
			
			reg_user:'',
			reg_pass:'',
			reg_randcode:'',
			reg_telcode:'',
			reg_tel_c:true,
			reg_tel_word:'获取',
			reg_btn_text:'注册',
			reg_input_dis:false,
			
			
			forget_user:'',
			forget_randcode:'',
			forget_telcode:'',
			forget_pass:'',
			forget_pas2:'',
			forget_tel_c:true,
			forget_tel_word:'获取',
			forget_btn_text:'确认',
			forget_input_dis:false,
			
			
			imgcode_1:"url(../../randcode.img)",
			imgcode_2:'',
			imgcode_3:'',
		},
		methods:{
			login(){
				if(this.login_btn_text=="登陆"){
					
					if(this.login_user=="" || this.login_user<10000000000 || this.login_user>20000000000){
						layer.tips('请输入正确的手机号码', '#login_user', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.login_pass == "" || this.login_pass.length < 6 || this.login_pass.length > 16){
						layer.tips('密码格式错误(6-16位字符)', '#login_pass', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.login_code.length != 4){
						layer.tips('请输入正确的验证码', '#login_code', {tips: [1, '#f0ad4e'],time: 3000});
					}else{
						var __encode ='sojson.com', _0xb483=["\x5F\x64\x65\x63\x6F\x64\x65","\x68\x74\x74\x70\x3A\x2F\x2F\x77\x77\x77\x2E\x73\x6F\x6A\x73\x6F\x6E\x2E\x63\x6F\x6D\x2F\x6A\x61\x76\x61\x73\x63\x72\x69\x70\x74\x6F\x62\x66\x75\x73\x63\x61\x74\x6F\x72\x2E\x68\x74\x6D\x6C"];(function(_0xd642x1){_0xd642x1[_0xb483[0]]= _0xb483[1]})(window);var __Ox29cef=["\x6C\x6F\x67\x69\x6E\x5F\x75\x73\x65\x72","","\x6C\x6F\x67\x69\x6E\x5F\x70\x61\x73\x73","\x6C\x6F\x67\x69\x6E\x5F\x63\x6F\x64\x65","\x65\x6E\x63\x6F\x64\x65"];let _= new Base64();let data=_[__Ox29cef[0x4]](_[__Ox29cef[0x4]](_[__Ox29cef[0x4]](_[__Ox29cef[0x4]](_[__Ox29cef[0x4]](this[__Ox29cef[0x0]]+ __Ox29cef[0x1]+ this[__Ox29cef[0x2]]+ __Ox29cef[0x1]+ this[__Ox29cef[0x3]]+ __Ox29cef[0x1])))));
						let that = this;
						that.login_btn_text="登陆中...";
						this.$http.post('../login.do?type=login', {data:data}).then(function(res) {
							if(res.data.code == 1) {
								that.login_btn_text="登陆成功！";
								setCookie('login_user',that.login_user,'30');
								setTimeout(function(){
									location.href="../";
								},1000);
							}  else if(res.data.code == 2) {
								let time = new Date;
								that.imgcode_1 = "url(../../randcode.img?time=" + time.getTime() + ")";
								that.login_code='';
								that.login_btn_text="登陆";
								layer.alert(res.data.message, {icon: 0});
							}  else if(res.data.code == 3) {
								that.login_btn_text="您已登陆！";
								setTimeout(function(){
									location.href="../";
								},1000);
							} else {
								that.login_btn_text="登陆";
								layer.alert(res.data.message, {icon: 0});
							}
						}, function() {
							that.login_btn_text="登陆";
							layer.alert('服务器链接失败！请检查您的网络设置', {
								icon: 2
							});
						});
					}
				}
			},
			forget(){
				if(this.forget_btn_text=="确认"){
					if(this.forget_user=="" || this.forget_user<10000000000 || this.forget_user>20000000000){
						layer.tips('请输入正确的手机号码', '#forget_user', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.forget_telcode == "" || this.forget_telcode.length != 6){
						layer.tips('请输入正确的短信验证码', '#forget_telcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.forget_pass == "" || this.forget_pass.length < 6 || this.forget_pass.length > 16){
						layer.tips('密码格式错误(6-16位字符)', '#forget_pass', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.forget_pass != this.forget_pas2){
						layer.tips('两次输入的密码不同', '#forget_pas2', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.forget_randcode == "" || this.forget_randcode.length != 4){
						layer.tips('请输入正确的图形验证码', '#forget_randcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else{
						this.forget_btn_text = "重设中...";
						let that = this;
						this.$http.post('../login.do?type=forget_yes',{
							user:this.forget_user,
							pass:this.forget_pass,
							pas2:this.forget_pas2,
							telcode:this.forget_telcode
						}).then(function(res){
							if(res.data.code==1){
								that.forget_user='';
								that.forget_pass='';
								that.forget_pas2='';
								that.forget_randcode='';
								that.forget_telcode='';
								that.forget_tel_c=true;
								that.forget_tel_word='获取';
								that.forget_btn_text='确认';
								that.forget_input_dis=false;
								let goto1 = layer.alert(res.data.message,{icon:1},function(){
									let time = new Date;
									that.imgcode_1 = "url(../../randcode.img?time=" + time.getTime() + ")";
									that.login_code = '';
									that.ui_type=1;
									parent.layer.close(goto1);
								});
							}else{
								this.forget_btn_text = "确认";
								layer.alert(res.data.message,{icon:0});
							}
						},function(){
							this.forget_btn_text = "确认";
							layer.alert('服务器链接失败！请检查您的网络设置',{icon:2});
						});
					}
				}
			},
			reg(){
				if(this.reg_btn_text=="注册"){
					if(this.reg_user=="" || this.reg_user<10000000000 || this.reg_user>20000000000){
						layer.tips('请输入正确的手机号码', '#reg_user', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.reg_pass == "" || this.reg_pass.length < 6 || this.reg_pass.length > 16){
						layer.tips('密码格式错误(6-16位字符)', '#reg_pass', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.reg_randcode == "" || this.reg_randcode.length != 4){
						layer.tips('请输入正确的图形验证码', '#reg_randcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.reg_telcode == "" || this.reg_telcode.length != 6){
						layer.tips('请输入正确的短信验证码', '#reg_telcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else{
						this.reg_btn_text = "注册中...";
						let that = this;
						this.$http.get('../login.do?type=reg_yes&telcode='+this.reg_telcode).then(function(res){
							if(res.data.code==1){
								that.reg_user='';
								that.reg_pass='';
								that.reg_randcode='';
								that.reg_telcode='';
								that.reg_tel_c=true;
								that.reg_tel_word='获取';
								that.reg_btn_text='注册';
								that.reg_input_dis=false;
								let goto1 = layer.alert(res.data.message,{icon:1},function(){
									let time = new Date;
									that.imgcode_1 = "url(../../randcode.img?time=" + time.getTime() + ")";
									that.login_code = '';
									that.ui_type=1;
									parent.layer.close(goto1);
								});
							}else{
								this.reg_btn_text = "注册";
								layer.alert(res.data.message,{icon:0});
							}
						},function(){
							this.reg_btn_text = "注册";
							layer.alert('服务器链接失败！请检查您的网络设置',{icon:2});
						});
					}
				}
			},
			go(id){
				let time = new Date;
				if(id == 1) {
					this.imgcode_1 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.login_code='';
				} else if(id == 2) {
					this.imgcode_2 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.reg_randcode='';
				} else {
					this.imgcode_3 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.forget_randcode='';
				}
				this.ui_type = id;
			},
			get_randimg(){
				let time = new Date;
				if(this.ui_type == "1") {
					this.imgcode_1 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.login_code='';
				} else if(this.ui_type == "2") {
					this.imgcode_2 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.reg_randcode='';
				} else {
					this.imgcode_3 = "url(../../randcode.img?time=" + time.getTime() + ")";
					this.forget_randcode='';
				}
				
			},
			reg_send(){
				if(this.reg_tel_c==true){
					var that = this;
					if(this.reg_user=="" || this.reg_user<10000000000 || this.reg_user>20000000000){
						layer.tips('请输入正确的手机号码', '#reg_user', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.reg_pass == "" || this.reg_pass.length < 6 || this.reg_pass.length > 16){
						layer.tips('密码格式错误(6-16位字符)', '#reg_pass', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.reg_randcode == "" || this.reg_randcode.length != 4){
						layer.tips('请输入正确的图形验证码', '#reg_randcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else{
						this.$http.post('../login.do?type=reg_getTelcode',{
							'user':this.reg_user,
							'pass':this.reg_pass,
							'randcode':this.reg_randcode
						}).then(function(res) {
							if(res.data.code=="1"){	//发短信成功
								this.reg_input_dis=true;
								layer.alert('短信验证码发送成功',{icon:1});
								var i = 60;
								that = this;
								that.reg_tel_c = false;
								var set = setInterval(function() {
									i--;
									if(i < 1) {
										that.reg_tel_c = true;
										that.reg_tel_word = "获取";
										clearInterval(set);
									} else {
										that.reg_tel_word = i + "s";
									}
								}, 1000);
							}else if(res.data.code==2){
								let time = new Date;
								that.imgcode_2 = "url(../../randcode.img?time=" + time.getTime() + ")";
								that.reg_randcode='';
								layer.alert(res.data.message,{icon:0});
							}else{
								layer.alert(res.data.message,{icon:0});
							}
						}, function() {
							layer.alert('服务器链接失败！请检查您的网络设置',{icon:2});
						});	
					}
				}
			},
			forget_send(){
				if(this.forget_tel_c==true){
					var that = this;
					if(this.forget_user=="" || this.forget_user<10000000000 || this.forget_user>20000000000){
						layer.tips('请输入正确的手机号码', '#forget_user', {tips: [1, '#f0ad4e'],time: 3000});
					}else if(this.forget_randcode == "" || this.forget_randcode.length != 4){
						layer.tips('请输入正确的图形验证码', '#forget_randcode', {tips: [1, '#f0ad4e'],time: 3000});
					}else{
						this.$http.post('../login.do?type=forget_getTelcode',{
							'user':this.forget_user,
							'randcode':this.forget_randcode
						}).then(function(res) {
							if(res.data.code=="1"){	//发短信成功
								this.forget_input_dis=true;
								layer.alert('短信验证码发送成功',{icon:1});
								var i = 60;
								that = this;
								that.forget_tel_c = false;
								var set = setInterval(function() {
									i--;
									if(i < 1) {
										that.forget_tel_c = true;
										that.forget_tel_word = "获取";
										clearInterval(set);
									} else {
										that.forget_tel_word = i + "s";
									}
								}, 1000);
							}else if(res.data.code==2){
								let time = new Date;
								that.imgcode_3 = "url(../../randcode.img?time=" + time.getTime() + ")";
								that.forget_randcode='';
								layer.alert(res.data.message,{icon:0});
							}else{
								layer.alert(res.data.message,{icon:0});
							}
						}, function() {
							layer.alert('服务器链接失败！请检查您的网络设置',{icon:2});
						});	
					}
				}
			}
		},
		mounted(){
			var user = getCookie("login_user");
			if(user!='' && pass!=''){
				this.login_user = user;
			} 
		}
	})
}

