<?php
/**
 * 挂件：文件头的代码注释
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: fileNoteWidget 1.0 2020-04-24 18:19:09Z tommy $
 * @package Widget
 * @since 1.0
 */
namespace widgets;

use doitphp\core\Widget;

class fileNoteWidget extends Widget {

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

		//get note info
		$notationModel = $this->model('notationStorage');
		$noteInfo      = $notationModel->getNotation();

		//assign params
		$this->assign('noteInfo', $noteInfo);

		//display page
		$this->display();
	}

}