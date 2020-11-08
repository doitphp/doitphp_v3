<div class="pl20 bg-info">
    <h5>Server :</h5>
  </div>
  <div class="ml20 mr30 pl20">
		<div class="list-inline">
      <div class="list-inline-item list-label">Server Time :</div>
      <div class="list-inline-item"><?php echo date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']); ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server Domain : </div>
      <div class="list-inline-item"><?php echo $_SERVER['SERVER_NAME']; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server IP :</div>
      <div class="list-inline-item"><?php echo $_SERVER['SERVER_ADDR']; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server OS : </div>
      <div class="list-inline-item"><?php echo $operateSystem; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server OS Charset : </div>
      <div class="list-inline-item"><?php echo $_SERVER['HTTP_ACCEPT_LANGUAGE']; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server Software : </div>
      <div class="list-inline-item"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Server Web Port : </div>
      <div class="list-inline-item"><?php echo $_SERVER['SERVER_PORT']; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">PHP run mode : </div>
      <div class="list-inline-item"><?php echo strtoupper(php_sapi_name()); ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label"><span class="text-primary">WebApp Path</span> : </div>
      <div class="list-inline-item">
			<?php
				if (is_dir($webappPath)) {
          echo is_writable($webappPath) ? '<img src="' . $baseAssetUrl . '/images/check_right.gif"> <span class="text-success">' . $webappPath . '</span> (支持文件写入操作)' :  '<img src="' . $baseAssetUrl . '/images/check_error.gif"> <span class="text-danger"><b>'. $webappPath . '</b></span>  (注:<span class="text-danger">当前目录没有文件写入权限!</span>)';
				} else {
					echo '<span class="text-danger">注：WebApp目录</span> ', $webappPath, ' <span class="text-danger">不存在!</span> 请在“WebApp管理”页里创建项目文件及目录。';
				}
			?>
			</div>
    </div>
  </div>

  <div class="mt40 pl20 bg-info">
    <h5>PHP :</h5>
  </div>

  <div class="ml20 mr30 pl20 pb30">
    <div class="list-inline">
      <div class="list-inline-item list-label">PHP Version : </div>
      <div class="list-inline-item"><?php echo PHP_VERSION; if (version_compare(PHP_VERSION,"5.3.0","<")) { echo ' (<span class="red">对不起,当前PHP环境无法满足DoitPHP的运行要求: PHP 5.3.0或更高版本,必须的!</span>)'; } ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">PHPINFO :</div>
      <div class="list-inline-item"><?php echo (stripos('phpinfo', get_cfg_var('disable_functions')) === false) ? '<a href="' . $phpinfoUrl . '" target="_blank">支持</a>' : '不支持'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Safe Mode : </div>
      <div class="list-inline-item"><?php echo get_cfg_var('safe_mode') ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">display_errors : </div>
      <div class="list-inline-item"><?php echo get_cfg_var('display_errors') ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">register_globals : </div>
      <div class="list-inline-item"><?php echo get_cfg_var('register_globals') ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">magic_quotes_gpc :  </div>
      <div class="list-inline-item"><?php echo get_cfg_var('magic_quotes_gpc') ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">memory_limit : </div>
      <div class="list-inline-item"><?php echo get_cfg_var('memory_limit'); ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">post_max_size : </div>
      <div class="list-inline-item"><?php echo get_cfg_var('post_max_size'); ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">upload_max_filesize : </div>
      <div class="list-inline-item"><?php if (get_cfg_var('file_uploads')) {echo get_cfg_var('upload_max_filesize'); } else { echo '不允许上传'; } ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">max_execution_time :</div>
      <div class="list-inline-item"><?php echo get_cfg_var('max_execution_time'), ' (秒)'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">disable_functions : </div>
      <div class="list-inline-item"><?php $disable_functions = get_cfg_var('disable_functions'); if(!$disable_functions) { echo 'No'; } else { echo $disable_functions; }?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">$_SERVER vars : </div>
      <div class="list-inline-item"><?php echo $serverResult; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">GD extension :</div>
      <div class="list-inline-item"><?php echo $gdResult; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">SPL extension :</div>
      <div class="list-inline-item"><?php echo extension_loaded("SPL") ? 'Yes' : '<span class="red">No</span>'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Openssl extension : </div>
      <div class="list-inline-item"><?php echo extension_loaded("openssl") ? 'Yes' : '<span class="text-danger">No</span>'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Redis extension : </div>
      <div class="list-inline-item"><?php echo (extension_loaded("redis")) ? 'Yes' : 'No'; ?></div>
    </div>
    
    <div class="list-inline">
      <div class="list-inline-item list-label">Memcached extension : </div>
      <div class="list-inline-item"><?php echo (extension_loaded("memcached")) ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">PCRE extension :  </div>
      <div class="list-inline-item"><?php echo extension_loaded("pcre") ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">DOM extension :  </div>
      <div class="list-inline-item"><?php echo class_exists("DOMDocument",false) ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Reflection extension : </div>
      <div class="list-inline-item"><?php echo class_exists('Reflection', false) ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">SOAP extension : </div>
      <div class="list-inline-item"><?php echo extension_loaded("soap") ? 'Yes' : 'No'; ?></div>
    </div>

    <div class="list-inline">
      <div class="list-inline-item list-label">Supported databases : </div>
      <div class="list-inline-item"><?php echo $databaseInfo; ?></div>
    </div>
    
    <div class="list-inline">
      <div class="list-inline-item list-label"></div>
      <div class="list-inline-item pl20"><a href="<?php echo $phpinfoUrl; ?>" target="_blank">More &gt;&gt;</a></div>
    </div>

  </div>