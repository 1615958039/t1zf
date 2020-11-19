# T1支付 iapp后台聚合接口管理系统

安装教程&说明：

1  准备服务器lnmp环境,php>7.0,mysql==5.5 (建议使用宝塔)

2  上传源码到服务器web目录下

3  准备数据库，导入data.sql (后台管理员账号: 10000000000 密码 t1zf.com )

4  用编辑器打开 /lib/function.php 修改里面的相关配置

5  配置nginx伪静态,代码在本页最下方，建议使用宝塔直接粘贴进伪静态即可

6  用浏览器访问 -> http://域名/chack.php 检查函数库及验证码是否正常可用

7  自行测试整站


8  挂监控 -> http://域名/control.do 和 http://域名/cz_control.do
建议频率建议越快越好,
control.do是订单监控，负责支付成功的回调处理,
cz_control.do是充值订单监控，负责后台用户充值的回调处理

9. 如有疑问，联系作者Q : 1615958039; 官方q群 : 792913779


增加管理员余额请使用phpmyadmin直接打开admin表操作，未写超级管理员后台


开源时间为2019/07/26，如有BUG请及时与我(Q1615958039)反馈

####**网站源码结构简介:**
```
├─/admin/		-> 后台管理系统前端文件(h5,js,css,第三方库)
├─/file/		-> 云盘系统的文件存储路径
├─/lib/			-> php代码存放目录
│  ├─/admin/	-> 后台管理系统的后端接口
│  │  ├─admin.php 		-> 获取管理员数据和推出登陆
│  │  ├─apk.php 		-> apk下载介绍修改接口
│  │  ├─change.php 		-> 修改管理员密码
│  │  ├─choujiang.php 	-> 抽奖系统列表
│  │  ├─file.php 		-> 云盘系统接口
│  │  ├─goods.php 		-> 支付_商品系统
│  │  ├─km.php 			-> 自动发卡系统
│  │  ├─log.php 		-> 管理员登陆日志
│  │  ├─login.php 		-> 注册登陆忘记密码接口 
│  │  ├─mail.php 		-> 邮件系统列表和配置
│  │  ├─mine.php 		-> 首页统计图及相关接口数据
│  │  ├─moneylog.php 	-> 余额系统日志
│  │  ├─pay_config.php 	-> 支付接口配置
│  │  ├─pay_list 		-> 支付订单列表
│  │  ├─telboom.php 	-> 短信轰炸机列表及配置页
│  │  ├─textlist.php 	-> 文本系统列表
│  │  ├─userqdonline.php-> 签到在线用户列表
│  │  ├─usersconfig.php -> 用户系统配置
│  │  ├─userslist.php 	-> 用户列表
│  │  ├─userslog.php 	-> 用户操作日志
│  │
│  ├─/api/ 		-> 非用户系统接口，接口商城
│  │  ├─/lib/ 	-> 天气接口配置文件
│  │  ├─api.php 		-> 天气\ping\ip 接口
│  │
│  ├─/lib/ 		-> phpmailer相关文件
│  ├─/pay/		-> 支付相关
│  │  ├─/lib/ 	-> 支付相关签名文件
│  │  ├─contorl.php  	-> 订单交易监控+云轰炸机
│  │  ├─cz_control.php 	-> 充值监控+云轰炸机
│  │  ├─e_notify.php 	-> 易支付异步通知
│  │  ├─index.php 		-> 支付入口
│  │  ├─ispay.php 		-> 支付判断接口
│  │  ├─m_notify.php 	-> 码支付异步通知
│  │
│  ├─/users/ 	-> 用户+支付系统接口
│  │  ├─all.php 		-> 其他接口
│  │  ├─api.php 		-> 用户系统其他接口
│  │  ├─index.php 		-> 用户注册登陆接口
│  │
│  ├─apk.php 		-> apk下载页
│  ├─code.php 		-> 验证码生成页
│  ├─config.php 	-> 系统配置
│  ├─fileput.php 	-> 文件输出下载页
│  ├─function.php 	-> 系统自定义函数
│
├─/src/ 		-> 前端资源文件
├─chack.php 	-> 源码安装环境检查
├─file.html 	-> 网盘下载页
├─index.html 	-> 网站入口,首页介绍页
├─rt_0.html 	-> 支付回调页
├─data.sql		-> 数据库文件,需要导入数据库
├─demo.iapp		-> iapp对接demo
```


####**数据库表简介:**
```
admin 			-> 后台管理员数据表
admin_info	 	-> 管理员资料表
apk	 			-> 安装包数据表
download_log	-> 文件下载日志
file			-> 云盘文件表
log_admin_login	-> 管理员登陆日志
log_admin_money	-> 管理员余额日志
log_admin_sms	-> 短信接口防刷记录
log_admin_sys	-> 管理员操作日志
pay_config		-> 支付接口配置
pay_goods	 	-> 商品系统商品数据
pay_goods_km	-> 商品系统自动发卡系统卡密
pay_order		-> 支付订单
pay_order_mail	-> 支付订单等待邮箱通知
smtp_config		-> 邮箱接口配置信息
smtp_log		-> 发件日志
telboom			-> 电话轰炸机日志
telboom_bmd		-> 电话轰炸机白名单
textlist		-> 远程文本
users			-> 用户表
users_choujiang	-> 用户抽奖奖品和日志
users_config	-> 用户系统配置
users_log_all	-> 用户签到\在线日志
users_log_custom-> 用户自定义变量日志
users_log_jf	-> 用户积分日志
users_log_money	-> 用户余额日志
users_log_vip	-> 用户vip日志
```

####**nginx伪静态:**
```html
rewrite ^/randcode.img /lib/code.php;
rewrite ^/admin/login.do /lib/admin/login.php;
rewrite ^/randcode.img /lib/code.php;
rewrite ^/admin/login.do /lib/admin/login.php;
rewrite ^/admin/change.do /lib/admin/change.php; 
rewrite ^/admin/log.do /lib/admin/log.php;
rewrite ^/admin/user.info /lib/admin/admin.php;
rewrite ^/admin/pay.config /lib/admin/pay_config.php;
rewrite ^/admin/pay.list /lib/admin/pay_list.php;
rewrite ^/admin/goods.list /lib/admin/goods.php;
rewrite ^/admin/users.list /lib/admin/userslist.php;
rewrite ^/admin/file.up /lib/admin/file.php;
rewrite ^/admin/money.log /lib/admin/moneylog.php;
rewrite ^/admin/mine.json /lib/admin/mine.php;
rewrite ^/admin/mail.json /lib/admin/mail.php;
rewrite ^/admin/userslog.json /lib/admin/userslog.php;
rewrite ^/admin/textlist.json /lib/admin/textlist.php;
rewrite ^/admin/usersconfig.json /lib/admin/usersconfig.php;
rewrite ^/admin/users.log /lib/admin/userqdonline.php;
rewrite ^/admin/apk.json /lib/admin/apk.php;
rewrite ^/admin/cj.json /lib/admin/choujiang.php;
rewrite ^/epay.notify /lib/pay/e_notify.php;
rewrite ^/mpay.notify /lib/pay/m_notify.php;
rewrite ^/control.do /lib/pay/control.php;
rewrite ^/cz_control.do /lib/pay/cz_control.php;
rewrite ^/pay.do /lib/pay/index.php;
rewrite ^/payrt.json /lib/pay/ispay.php;
rewrite	^/user_reg.json /lib/users/index.php;
rewrite ^/user_info.json /lib/users/api.php;
rewrite	^/user_all.json /lib/users/all.php;
rewrite	^/down /lib/apk.php;
rewrite ^/d /lib/fileput.php;
rewrite ^/apk /lib/apk.php;
rewrite	^/api.json /lib/api/api.php;
rewrite	^/xyy.json /lib/api/xyy.php;
rewrite	^/admin/xyy.json /lib/admin/xyy.php;
rewrite	^/admin/km.json /lib/admin/km.php;
rewrite	^/admin/telboom.json /lib/admin/telboom.php;
```

