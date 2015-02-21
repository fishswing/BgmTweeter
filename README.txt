Properer Bangumi RSS
v1.1a by fishswing <me@swingworks.net> 2013-07-07
http://www.swingworks.net

该项目的feed生成模块使用了feedcreator库（版本1.8）并对其进行了一些修改，
该库文件保持LGPL协议进行授权，其源代码和授权文本的副本位于feedcreator目录下。
该项目的新浪微博发布模块使用了新浪微博开放平台的官方PHP SDK并对其进行了一些修改。
该项目的Twitter发布模块使用了abraham的twitteroauth库并对其进行了一些修改。
该项目抓取和处理的信息由Bangumi番组计划(bgm.tv)提供，其权利属于各自的版权方和用户。
项目的其余代码采用MIT License进行授权，其授权文本位于根目录下的LICENSE.txt。

使用本项目即代表您默认遵守所有相应的协议。

--

该项目是一个改进版的Bangumi RSS输出，基于PHP 5以上且支持curl的主机运行。
目前Bangumi原生RSS功能的效果很差，
利用该项目可以输出一个可读性更好的RSS，方便同步到Twitter或微博等社交网络上。
目前项目已经包括了新浪微博和Twitter的自动发布功能。

详细介绍请点击：
http://www.swingworks.net/2012/04/propbgmrss/

项目主页在
http://code.google.com/p/propbgmrss/

--

v1.0b升级到v1.1a说明
将getmyrss.php以及twitter/twitteroauth目录下的两个文件覆盖即可。

v1.0升级到v1.0b说明：
将getmyrss.php, weibo/sendweibo.php和twitter/sendtwitter.php三个文件覆盖即可。
如当前已有番组中文名错误，请将服务器目录下的titlecache.id文件删除来清除番组名缓存。

--

使用说明：

1. 修改config.php填入配置信息，其中$bgm_type目前仅支持progress(进度)和subject(收藏)。

2. 如需使用微博或Twitter发布功能，请使用cron来定期执行index.php，方法是先在cron.php中
   填入index.php的url，然后在后台面板的“计划任务”或“时间守护任务”中新建一条指令，形如：
   php /home/<username>/domains/<yourdomain>/public_html/propbgmrss/cron.php >/dev/null 2>&1
   推荐将其运行频率调为每5分钟(*/5)到每10分钟(*/10)。（一开始可以设快一些看看设置是否正确）
   最后在config.php中把$is_redirect设为false。

3a.如需要使用新浪微博发布功能，请先配置好cron，然后到新浪创建一个app，方法为：
1) 到微博开放平台应用开发页(http://open.weibo.com/development)创建一个“网页应用”；
2) 将“应用地址”设置为 http://<yourdomain>/propbgmrss/weibo/，填好其它相关信息，点“创建”；
3) 如创建成功，请在“高级信息”将“授权回调页”和“取消授权回调页”设置为
   http://<yourdomain>/propbgmrss/weibo/callback.php
   然后记录下App-Key和App-Secret，关闭页面；
4) 在weibo/config.php填入App-Key，App-Secret和回调页地址(Callback URL)；
5) 在根目录的config.php中将$weibo_enabled设为true，并设置好$weibo_pattern；
6) 在任何时候（包括今后）都请勿将app提交审核！

3b.如需要使用Twitter发布功能，请先配置好cron，然后到Twitter创建一个app，方法为：
1) 到Twitter Developers页面(https://dev.twitter.com/)点击“Create an app”；
2) 将“Callback URL”设置为 http://<yourdomain>/propbgmrss/twitter/callback.php；
3) 如创建成功，会进入Application页面，点击“Settings”选项卡；
4) 将“Application Type”中的“Access”选择为“Read and Write”，然后点底部的“Update”；
5) 回到“Details”选项卡，记录Consumer key和Consumer secret，填入twitter/config.php；
6) 在根目录的config.php中将$twitter_enabled设为true，并设置好$twitter_pattern；

4. 将整个propbgmrss目录上传到php服务器。
   如配置了微博功能，请访问 http://<yourdomain>/propbgmrss/weibo/ 完成授权。
   如配置了Twitter功能，请访问 http://<yourdomain>/propbgmrss/twitter/ 完成授权。
   然后访问一次 http://<yourdomain>/propbgmrss/ 测试是否成功生成rss。
   如未配置过cron，rss地址是 http://<yourdomain>/propbgmrss/
   如已配置了cron，则rss地址变为 http://<yourdomain>/propbgmrss/rss.xml
   以后将rss阅读器指向这个地址即可。

5. 由于feedcreator库的限制，如果你需要同时生成多个feed（如进度和收藏各一个），
   请拷贝index.php和config.php为index2.php和config2.php，
   在config.php和config2.php中给两个$rss_filename设置不同的文件名，
   然后修改index2.php的require_once("config.php")为require_once("config2.php")。
   如果使用cron，还需将index2.php添加到cron.php中。

--

更新日志：

2013-07-07 v1.1a
1.修正番组话数为.5时显示错位的问题。

2013-07-06 v1.1
1.支持Twitter API v1.1。

2013-04-30 v1.0b
1.修正番组中文名错误的问题。

2013-04-05 v1.0a
1.增加自动截断功能，防止字数超出140字限制导致发送失败。

2012-11-14 v1.0
1.改进新浪微博授权页的显示方式，提示有效期；
2.修正总话数为??的番组在某些情况下不能更新feed的问题；
3.修正新浪SDK的一处缺陷导致未开启短标签的服务器无法授权的问题。

2012-09-03 v0.9
1.修正因Bangumi网站微调造成番组中文名无法显示的问题；
2.升级到新浪微博OAuth V2端口；
3.针对新浪微博V2端口的机制进行优化，允许原用户更新授权，提示有效期；
4.增加授权页显示当前运行状态。

2012-06-10 v0.8
1.增加Twitter自动发布功能；
2.增加了oauth文件检查，文件已存在时禁止授权操作以防止被他人冒用；
3.修改OAuth库代码解决多微博冲突问题；
4.新增了两个微博发布pattern支持的字段；
5.微调番组中文名显示样式；
6.代码精简优化。

2012-06-02 v0.7
1.完善了新浪微博自动发布功能。改为使用oauth1授权，现在起授权一次就可以长期运行了。

2012-05-26 v0.5
1.增加中文番组名显示。

2012-04-10 v0.4
1.增加新浪微博自动发布功能。

2012-04-03 v0.3
第一个发布版本
1.去除冗余循环检查；
2.修正书籍进度不能显示的问题；
3.修正进度标题过长导致截断的问题。

2012-03-31 v0.2
测试版本
