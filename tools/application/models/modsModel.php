<?php
/**
 * 模型：Model文件信息管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: modsModel.php 1.0 2020-04-18 13:48:52Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

class modsModel extends dataStorageModel {

  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'models_storage.data.php';
	}
	
}