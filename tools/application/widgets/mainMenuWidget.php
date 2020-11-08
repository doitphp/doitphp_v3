<?php
/**
 * 挂件：主菜单
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: mainMenuWidget 1.0 2020-04-24 07:02:42Z tommy $
 * @package Widget
 * @since 1.0
 */
namespace widgets;

use doitphp\core\Widget;
use doitphp\App;

class mainMenuWidget extends Widget {

	/**
	 * main method
	 *
	 * @access public
	 *
	 * @param array $params 参数
	 *
	 * @return void
	 */
	public function renderContent($params = null) {

		//get controller name and Action name.
		$controllerName = App::getControllerName();

		//分析项目根目录
		$webappPath    = $params['basePath'];
		$projectStatus = is_dir($webappPath . DS . 'application') ? true : false;

		//主菜单链接
		$menuLinks['index'] = array(
			'text'        => '主页', 
			'link'        => $this->createUrl('index/index'), 
			'isActivated' => ($controllerName == 'index') ? true : false
		);
		
		if(!$projectStatus){
			$menuLinks['webapp'] = array(
				'text'        => 'WebAPP管理', 
				'link'        => $this->createUrl('webapp/index'), 
				'isActivated' => ($controllerName == 'webapp') ? true : false
			);
		}else{
			//files
			$menuLinks['files'] = array(
				'text'    		=> '文件管理', 
				'link'        => $this->createUrl('files/index'),
				'isActivated' => ($controllerName == 'files') ? true : false
			);

			//controller
			$menuLinks['ctls'] = array(
				'text'				=> 'Controller文件管理', 
				'link'    		=> $this->createUrl('ctls/index'),
				'isActivated' => ($controllerName == 'ctls') ? true : false
			);

			//model
			$menuLinks['mods'] = array(
				'text'				=> 'Model文件管理', 
				'link'				=> $this->createUrl('mods/index'),
				'isActivated' => ($controllerName == 'mods') ? true : false
			);

			//widget
			$menuLinks['wids'] = array(
				'text'				=> 'Widget文件管理', 
				'link'				=> $this->createUrl('wids/index'),
				'isActivated' => ($controllerName == 'wids') ? true : false
			);

			//library
			$menuLinks['libs'] = array(
				'text'				=> '类文件管理', 
				'link'				=> $this->createUrl('libs/index'),
				'isActivated' => ($controllerName == 'libs') ? true : false
			);

			//extension
			$menuLinks['exts'] = array(
				'text'				=> '扩展模块管理', 
				'link'        => $this->createUrl('exts/index'),
				'isActivated' => ($controllerName == 'exts') ? true : false
			);
		}

		//assign params
		$this->assign(array(
			'menuLinks' => $menuLinks,
		));

		//display page
		$this->display();
	}

}