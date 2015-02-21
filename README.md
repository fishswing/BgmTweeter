## BgmTweeter v1.2  (aka propbgmrss) ##
fishswing 2015-02-21  http://www.swingworks.net

BgmTweeter是一个Bangumi的Twitter和微博同步工具，  
同时是一个改进版的Bangumi RSS输出，基于PHP 5以上且支持curl的主机运行。

目前Bangumi原生RSS功能的效果很差，  
利用该项目可以输出一个可读性更好的RSS，同时自动发布到Twitter或微博上。

RSS输出示例：samples/rss_subject.xml，samples/rss_progress.xml  
微博发布示例：http://s.weibo.com/wb/魚尾の補番計画&nodup=1
Twitter发布示例：https://twitter.com/search?q=鱼尾de补番计画

详细介绍请点击：  
http://www.swingworks.net/2012/04/propbgmrss/

项目主页在  
https://github.com/fishswing/BgmTweeter

--

该项目的Twitter发布模块使用了abraham的twitteroauth(版本0.5.1)。  
该项目的新浪微博发布模块使用了新浪微博开放平台的官方PHP SDK并对其进行了一些修改。  
该项目的feed生成模块使用了feedcreator（版本1.8）并对其进行了一些修改。  
以上库文件保持各自协议进行授权，其授权文本的副本位于相应的目录下。  
该项目抓取和处理的信息由Bangumi番组计划(bgm.tv)提供，其权利属于各自的版权方和用户。  
项目的其余代码采用MIT License进行授权，其授权文本位于根目录下的LICENSE.txt。  

使用本项目即代表您默认遵守所有相应的协议。

--

### 升级说明 ###

#### 【v1.1a升级到v1.2说明】 ####
1. 请首先备份`config.php`和`cron.php`；
2. 删除`twitter/twitteroauth`下（非twitter目录）的所有文件；
3. 将所有文件上传覆盖；
4. 对照备份下来的旧`config.php`配置新的`config.php`；
5. 将备份下来的`cron.php`覆盖回去；
6. 最后将`titlecache.id`删除。

--

### 使用说明 ###

BgmTweeter可以部署为自动发布工具，也可以作为RSS输出。
  


#### 【如何部署为RSS输出】 ####

如果你不懂得如何配置服务器的cron任务，或者烦于申请app，  
可按照以下配置为RSS输出，并且配合ifttt来达到自动发布的目的。

1. 在`config.php`中配置好用户名、RSS类别、标题、节目名样式等信息；
2. 将所有文件上传到php服务器；
3. RSS的地址即为 `http://<your_domain>/bgmtweeter/`
  


#### 【如何部署为RSS输出，并且使用cron】 ####

使用cron定时触发可以减少服务器压力，尤其是配置了多处RSS检查的情况下。  
这种情况下爬虫访问的是静态的xml页面，不会每次强制刷新。

1. 在`config.php`中配置好用户名、RSS类别、标题、节目名样式等信息；
2. 你可以修改`$rss_filename`为自己喜欢的xml文件名，默认为`rss.xml`；
3. 在`cron.php`中加入首页的完整url：`http://<your_domain>/bgmtweeter/index.php`
4. 将所有文件上传到php服务器；
5. 在服务器配置面板新建一个cron任务，推荐将其执行频率调整为10分钟左右：  
   `php /home/<your_domain_files_path>/bgmtweeter/cron.php >/dev/null 2>&1`  
   其中`<your_domain_files_path>`是后台管理文件的linux文件系统路径；
6. 先手工访问测试一下rss.xml是否生成：`http://<your_domain>/bgmtweeter/`
7. RSS的地址即为 `http://<your_domain>/bgmtweeter/rss.xml`
  


#### 【如何自动发布Twitter和微博】 ####

自动发布Twitter和微博需要配置好cron，而且需要申请一个app用于个人发布。

1. 如需要发布微博，请先到新浪创建一个app，方法为：  
1) 到微博开放平台 (http://open.weibo.com/development) 点击“创建应用”，然后选“网页应用”；  
2) 将“应用地址”设置为`http://<your_domain>/bgmtweeter/weibo/`，填好其它相关信息，点“创建”；  
3) 创建成功后，在“高级信息”将“授权回调页”和“取消授权回调页”设置为  
   `http://<your_domain>/bgmtweeter/weibo/callback.php`  
   然后记录下App-Key和App-Secret；  
4) 在`weibo/config.php`填入`App-Key`，`App-Secret`和`回调页地址(Callback URL)`；  
5) 在根目录的config.php中将`$weibo_enabled`设为`true`，并设置好`$weibo_pattern`；  
6) 由于微博API的限制，在任何时候（包括今后）都请勿将app提交审核！  

2. 如需要发布Twitter，请先到Twitter创建一个app，方法为：  
1) 到Twitter Developers页面 (https://apps.twitter.com/) 点击“Create New App”；  
2) 填写相关信息，并将“Callback URL”设置为  
   `http://<your_domain>/bgmtweeter/twitter/callback.php`  
3) 创建成功后，会进入Application页面，点击“Permissions”选项卡；  
4) 将“Access”选择为“Read and Write”，然后点底部的“Update Settings”；  
5) 点击“Keys and Access Tokens”选项卡，记录Consumer Key和Consumer Secret，填入`twitter/config.php`；  
6) 在根目录的config.php中将`$twitter_enabled`设为`true`，并设置好`$twitter_pattern`。

3. 完成上节“如何部署为RSS输出，并且使用cron”的第1步到第4步；
4. 如配置了微博功能，请访问 `http://<your_domain>/bgmtweeter/weibo/` 完成授权；  
   如配置了Twitter功能，请访问 `http://<your_domain>/bgmtweeter/twitter/` 完成授权；
5. 完成上节“如何部署为RSS输出，并且使用cron”的剩余步骤。
  
  

#### 【如何同时生成和发布不同种类的feed】 ####

1. 由于feedcreator库的限制，如果你需要同时生成多个feed（如进度和收藏各一个），  
请拷贝`index.php`和`config.php`为`index2.php`和`config2.php`，  
2. 在`config.php`和`config2.php`中设置不同的`$rss_type`和`$rss_filename`，  
3. 将`index2.php`的`require_once("config.php")`修改为`require_once("config2.php")`。  
4. 如果使用cron，还需将`index2.php`添加到`cron.php`中。

--

### 更新日志 ###

##### 2015-02-21 v1.2 #####
1. propbgmrss现在改名为BgmTweeter；
2. 更新twitteroauth，支持最新的Twitter API v1.1；
3. 发布和RSS生成现在分离成两个独立的功能；
4. RSS标题可自定义，番组名字可以自定义中日文样式；
5. 代码优化。

##### 2013-07-07 v1.1a #####
1. 修正番组话数为.5时显示错位的问题。

##### 2013-07-06 v1.1 #####
1. 支持Twitter API v1.1。

##### 2013-04-30 v1.0b #####
1. 修正番组中文名错误的问题。

##### 2013-04-05 v1.0a #####
1. 增加自动截断功能，防止字数超出140字限制导致发送失败。

##### 2012-11-14 v1.0 #####
1. 改进新浪微博授权页的显示方式，提示有效期；
2. 修正总话数为??的番组在某些情况下不能更新feed的问题；
3. 修正新浪SDK的一处缺陷导致未开启短标签的服务器无法授权的问题。

##### 2012-09-03 v0.9 #####
1. 修正因Bangumi网站微调造成番组中文名无法显示的问题；
2. 升级到新浪微博OAuth V2端口；
3. 针对新浪微博V2端口的机制进行优化，允许原用户更新授权，提示有效期；
4. 增加授权页显示当前运行状态。

##### 2012-06-10 v0.8 #####
1. 增加Twitter自动发布功能；
2. 增加了oauth文件检查，文件已存在时禁止授权操作以防止被他人冒用；
3. 修改OAuth库代码解决多微博冲突问题；
4. 新增了两个微博发布pattern支持的字段；
5. 微调番组中文名显示样式；
6. 代码精简优化。

##### 2012-06-02 v0.7 #####
1. 完善了新浪微博自动发布功能。改为使用oauth1授权，现在起授权一次就可以长期运行了。

##### 2012-05-26 v0.5 #####
1. 增加中文番组名显示。

##### 2012-04-10 v0.4 #####
1. 增加新浪微博自动发布功能。

##### 2012-04-03 v0.3 #####
第一个发布版本

1. 去除冗余循环检查；
2. 修正书籍进度不能显示的问题；
3. 修正进度标题过长导致截断的问题。

##### 2012-03-31 v0.2 #####
测试版本
