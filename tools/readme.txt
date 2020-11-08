DoitPHP Tools V3.2
=============================

感谢您选用doitphp tools，这是一个doitphp的辅助开发工具。使用本程序，可在快速开发项目代码。

从doitphp v2.1开始，doitphp tools将独立存在，不再与doitphp框架文件捆绑。

原因有二：
1、便于项目代码的管理。毕竟doitphp tools仅仅是在开发环境才使用的东东。
doitphp tools独立出来，项目代码使用svn, git版本控制管理工具向线上服务器部署代码时，更加方便。
2、doitphp tools独立存在，脱离了application目录的束缚，使tools的使用更加灵活。
如：doitphp tools未独立之前，当项目开启rewrite功能时。doitphp tools就无法访问（虽然可以通过更改rewrite规则来解决，总感觉很麻烦）。


使用方法：

1.设置所要开发项目的目录路径
打开入口文件：index.php

#21 -- #29

/**
 * 自定义DoitPHP框架目录文件所在路径。注：结尾无需"/"。
 */
define('DOITPHP_PATH', APP_ROOT . '/../doitphp');

/**
 * 自定义所要创建及管理项目(project)的目录路径。注：结尾无需"/"。
 */
define('WEB_APP_PATH', substr(APP_ROOT, 0, -6));

将DOITPHP_PATH、WEB_APP_PATH更改为实际的目录路径。

2、配置数据库连接参数
打开配置文件：application/config/application.php

#30 -- #37

//设置数据库连接参数
$config['db'] = array(
	'dsn'      => 'mysql:host=localhost;dbname=yourDbname',
	'username' => 'yourUserName',
	'password' => 'yourPassword',
	'charset'  => 'utf8',
	'prefix'   => '',
);

根据实际情况设置数据库连接参数。

3、运行 index.php 。默认登录用户名及密码分别为: doitphp, 123456
如需更改用户名及密码, 打开配置文件：application/config/application.php

#18 -- #22

//设置登陆用户及密码
$config['loginUser'] = array(
	'username'=>'doitphp',
	'password'=>123456,
);

不细说了，你们懂得。

4、如果使用doitphp tools生成项目目录。生成的项目文件index.php代码需改下代码。
打开生成的index.php

#19 -- #22

/**
 * 加载DoitPHP框架的初始化文件,如果必要可以修改文件路径
 */
require_once APP_ROOT . '/doitphp/DoitPHP.php';

就是上面require_once的文件(DoitPHP.php)路径。如果不实，请更改一下。
因为doitphp tools独立之后，DoitPHP.php的路径相对创建的目录灵活多了。doitphp tools不能准确判断出来。



注：最后再重申一下：doitphp tools 仅只是在开发环境下运行的。切不可将代码传到线上服务器上啊！后果很严重。原因自己想去吧。



要求
------------

基本要求:web服务器运行的PHP版本5.3.0或以上,且支持gd及spl扩展. 