<?php
/**
 * 模型：类文件(library)信息管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: libsModel.php 1.0 2020-04-18 13:51:49Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

class libsModel extends dataStorageModel {

  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'library_storage.data.php';
	}
  
}