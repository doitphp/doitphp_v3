<?php
/**
 * 模型：扩展引导文件信息管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: extsModel.php 1.0 2020-04-18 13:53:32Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

class extsModel extends dataStorageModel {

  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'extensions_storage.data.php';
	}

}