<?php
/**
 * 模型：挂件文件(widget)的信息管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: widgetsModel.php 1.0 2020-04-18 13:51:14Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

class widgetsModel extends dataStorageModel {

  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'widgets_storage.data.php';
	}
  
}