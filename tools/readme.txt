DoitPHP Tools V3.2
=============================

��л��ѡ��doitphp tools������һ��doitphp�ĸ����������ߡ�ʹ�ñ����򣬿��ڿ��ٿ�����Ŀ���롣

��doitphp v2.1��ʼ��doitphp tools���������ڣ�������doitphp����ļ�����

ԭ���ж���
1��������Ŀ����Ĺ����Ͼ�doitphp tools�������ڿ���������ʹ�õĶ�����
doitphp tools������������Ŀ����ʹ��svn, git�汾���ƹ����������Ϸ������������ʱ�����ӷ��㡣
2��doitphp tools�������ڣ�������applicationĿ¼��������ʹtools��ʹ�ø�����
�磺doitphp toolsδ����֮ǰ������Ŀ����rewrite����ʱ��doitphp tools���޷����ʣ���Ȼ����ͨ������rewrite������������ܸо����鷳����


ʹ�÷�����

1.������Ҫ������Ŀ��Ŀ¼·��
������ļ���index.php

#21 -- #29

/**
 * �Զ���DoitPHP���Ŀ¼�ļ�����·����ע����β����"/"��
 */
define('DOITPHP_PATH', APP_ROOT . '/../doitphp');

/**
 * �Զ�����Ҫ������������Ŀ(project)��Ŀ¼·����ע����β����"/"��
 */
define('WEB_APP_PATH', substr(APP_ROOT, 0, -6));

��DOITPHP_PATH��WEB_APP_PATH����Ϊʵ�ʵ�Ŀ¼·����

2���������ݿ����Ӳ���
�������ļ���application/config/application.php

#30 -- #37

//�������ݿ����Ӳ���
$config['db'] = array(
	'dsn'      => 'mysql:host=localhost;dbname=yourDbname',
	'username' => 'yourUserName',
	'password' => 'yourPassword',
	'charset'  => 'utf8',
	'prefix'   => '',
);

����ʵ������������ݿ����Ӳ�����

3������ index.php ��Ĭ�ϵ�¼�û���������ֱ�Ϊ: doitphp, 123456
��������û���������, �������ļ���application/config/application.php

#18 -- #22

//���õ�½�û�������
$config['loginUser'] = array(
	'username'=>'doitphp',
	'password'=>123456,
);

��ϸ˵�ˣ����Ƕ��á�

4�����ʹ��doitphp tools������ĿĿ¼�����ɵ���Ŀ�ļ�index.php��������´��롣
�����ɵ�index.php

#19 -- #22

/**
 * ����DoitPHP��ܵĳ�ʼ���ļ�,�����Ҫ�����޸��ļ�·��
 */
require_once APP_ROOT . '/doitphp/DoitPHP.php';

��������require_once���ļ�(DoitPHP.php)·���������ʵ�������һ�¡�
��Ϊdoitphp tools����֮��DoitPHP.php��·����Դ�����Ŀ¼�����ˡ�doitphp tools����׼ȷ�жϳ�����



ע�����������һ�£�doitphp tools ��ֻ���ڿ������������еġ��в��ɽ����봫�����Ϸ������ϰ�����������ء�ԭ���Լ���ȥ�ɡ�



Ҫ��
------------

����Ҫ��:web���������е�PHP�汾5.3.0������,��֧��gd��spl��չ. 