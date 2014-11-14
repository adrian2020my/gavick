<?php
/**
 *  * @version $Id$
 * Configuration helper class
 *
 * This class provides some functions that are used throughout the VirtueMart shop to access configuration values.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009-2014 VirtueMart Team. All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');

/**
 *
 * We need this extra paths to have always the correct path undependent by loaded application, module or plugin
 * Plugin, module developers must always include this config at start of their application
 *   $vmConfig = VmConfig::loadConfig(); // load the config and create an instance
 *  $vmConfig -> jQuery(); // for use of jQuery
 *  Then always use the defined paths below to ensure future stability
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define( 'JPATH_VM_SITE', JPATH_ROOT.DS.'components'.DS.'com_virtuemart' );
defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart');
// define( 'JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart' );
define( 'JPATH_VM_PLUGINS', JPATH_VM_ADMINISTRATOR.DS.'plugins' );
define( 'JPATH_VM_MODULES', JPATH_ROOT.DS.'modules' );

if(version_compare(JVERSION,'1.7.0','ge')) {
	defined('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_PLATFORM);
	defined('JVM_VERSION') or define ('JVM_VERSION', 2);
}
else {
	if (version_compare (JVERSION, '1.6.0', 'ge')) {
		defined ('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_LIBRARIES);
		defined ('JVM_VERSION') or define ('JVM_VERSION', 2);
	}
	else {
		defined ('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_LIBRARIES);
		defined ('JVM_VERSION') or define ('JVM_VERSION', 1);
	}
}

//This number is for obstruction, similar to the prefix jos_ of joomla it should be avoided
//to use the standard 7, choose something else between 1 and 99, it is added to the ordernumber as counter
// and must not be lowered.
defined('VM_ORDER_OFFSET') or define('VM_ORDER_OFFSET',3);


require(JPATH_VM_ADMINISTRATOR.DS.'version.php');

JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

if(!class_exists('vRequest')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vrequest.php');
if(!class_exists('vmText')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtext.php');
if(!class_exists('vmJsApi')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmjsapi.php');

/**
 * This function shows an info message, the messages gets translated with JText::,
 * you can overload the function, so that automatically sprintf is taken, when needed.
 * So this works vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE',$type,$link )
 * and also vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE');
 *
 * @author Max Milbers
 * @param string $publicdescr
 * @param string $value
 */

function vmInfo($publicdescr,$value=NULL){

	$app = JFactory::getApplication();

	$msg = '';
	$type = 'info';
	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if (count($args) > 0) {
				$args[0] = $lang->_($args[0]);
				$msg = call_user_func_array('sprintf', $args);
			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			//$publicdescr = $lang->_($publicdescr);
			$msg = JText::_($publicdescr);
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$msg = 'Max messages reached';
			$type = 'warning';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app ->enqueueMessage($msg,$type);
	} else {
		vmTrace('vmInfo Message empty '.$msg);
	}

	return $msg;
}

/**
 * Informations for the vendors or the administrators of the store, but not for developers like vmdebug
 * @param      $publicdescr
 * @param null $value
 */
function vmAdminInfo($publicdescr,$value=NULL){

	if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
	if(Permissions::getInstance()->isSuperVendor()){

		$app = JFactory::getApplication();

		if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
			$lang = JFactory::getLanguage();
			if($value!==NULL){

				$args = func_get_args();
				if (count($args) > 0) {
					$args[0] = $lang->_($args[0]);
					VmConfig::$maxMessageCount++;
					$app ->enqueueMessage(call_user_func_array('sprintf', $args),'info');
				}
			}	else {
				VmConfig::$maxMessageCount++;
				// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
				$publicdescr = $lang->_($publicdescr);
				$app ->enqueueMessage('Info: '.JText::_($publicdescr),'info');
				// 		debug_print_backtrace();
			}
		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'info');
			}else {
				return false;
			}
		}
	}

}

function vmWarn($publicdescr,$value=NULL){


	$app = JFactory::getApplication();
	$msg = '';
	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if (count($args) > 0) {
				$args[0] = $lang->_($args[0]);
				$msg = call_user_func_array('sprintf', $args);

			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			$msg = $lang->_($publicdescr);
			//$app ->enqueueMessage('Info: '.$publicdescr,'warning');
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$msg = 'Max messages reached';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app ->enqueueMessage($msg,'warning');
		return $msg;
	} else {
		vmTrace('vmWarn Message empty');
		return false;
	}

}

/**
 * Shows an error message, sensible information should be only in the first one, the second one is for non BE users
 * @author Max Milbers
 */
function vmError($descr,$publicdescr=''){

	$msg = '';
	$lang = JFactory::getLanguage();
	$descr = $lang->_($descr);
	$adminmsg =  'vmError: '.$descr;
	if (empty($descr)) {
		vmTrace ('vmError message empty');
		return;
	}
	logInfo($adminmsg,'error');
	if(VmConfig::$maxMessageCount< (VmConfig::$maxMessage+5)){


		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}

		if(Permissions::getInstance()->check('admin')){
			$msg = $adminmsg;
		} else {
			if(!empty($publicdescr)){
				$msg = $lang->_($publicdescr);
			}
		}
	}
	else {
		if (VmConfig::$maxMessageCount == (VmConfig::$maxMessage+5)) {
			$msg = 'Max messages reached';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app = JFactory::getApplication();
		$app ->enqueueMessage($msg,'error');
		return $msg;
	}

	return $msg;

}

/**
 * A debug dumper for VM, it is only shown to backend users.
 *
 * @author Max Milbers
 * @param unknown_type $descr
 * @param unknown_type $values
 */
function vmdebug($debugdescr,$debugvalues=NULL){

	if(VMConfig::showDebug()  ){


		$app = JFactory::getApplication();

		if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
			if($debugvalues!==NULL){
				// 			$debugdescr .=' <pre>'.print_r($debugvalues,1).'<br />'.print_r(get_class_methods($debugvalues),1).'</pre>';

				$args = func_get_args();
				if (count($args) > 1) {
					// 				foreach($args as $debugvalue){
					for($i=1;$i<count($args);$i++){
						if(isset($args[$i])){
							$debugdescr .=' Var'.$i.': <pre>'.print_r($args[$i],1).'<br />'.print_r(get_class_methods($args[$i]),1).'</pre>';
						}
					}

				}
			}

			if(VmConfig::$echoDebug){
				VmConfig::$maxMessageCount++;
				echo $debugdescr;
			} else if(VmConfig::$logDebug){
				logInfo($debugdescr,'vmdebug');
			}else {
				VmConfig::$maxMessageCount++;
				$app = JFactory::getApplication();
				$app ->enqueueMessage('<span class="vmdebug" >vmdebug '.$debugdescr.'</span>');
			}

		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'info');
			}
		}

	}

}

function vmTrace($notice,$force=FALSE){

	if($force || (VMConfig::showDebug() ) ){
		//$app = JFactory::getApplication();
		//
		ob_start();
		echo '<pre>';
		debug_print_backtrace();
		echo '</pre>';
		$body = ob_get_contents();
		ob_end_clean();
		if(VmConfig::$echoDebug){
			echo $notice.' <pre>'.$body.'</pre>';
		} else if(VmConfig::$logDebug){
			logInfo($body,$notice);
		} else {
			$app = JFactory::getApplication();
			$app ->enqueueMessage($notice.' '.$body.' ');
		}

	}

}

function vmRam($notice,$value=NULL){
	vmdebug($notice.' used Ram '.round(memory_get_usage(TRUE)/(1024*1024),2).'M ',$value);
}

function vmRamPeak($notice,$value=NULL){
	vmdebug($notice.' memory peak '.round(memory_get_peak_usage(TRUE)/(1024*1024),2).'M ',$value);
}


function vmSetStartTime($name='current'){

	VmConfig::setStartTime($name, microtime(TRUE));
}

function vmTime($descr,$name='current'){

	if (empty($descr)) {
		$descr = $name;
	}
	$starttime = VmConfig::$_starttime ;
	if(empty($starttime[$name])){
		vmdebug('vmTime: '.$descr.' starting '.microtime(TRUE));
		VmConfig::$_starttime[$name] = microtime(TRUE);
	}
	else {
		if ($name == 'current') {
			vmdebug ('vmTime: ' . $descr . ' time consumed ' . (microtime (TRUE) - $starttime[$name]));
			VmConfig::$_starttime[$name] = microtime (TRUE);
		}
		else {
			if (empty($descr)) {
				$descr = $name;
			}
			$tmp = 'vmTime: ' . $descr . ': ' . (microtime (TRUE) - $starttime[$name]);
			vmdebug ($tmp);
		}
	}

}

/**
 * logInfo
 * to help debugging Payment notification for example
 */
function logInfo ($text, $type = 'message') {
	jimport('joomla.filesystem.file');
	$config = JFactory::getConfig();
	$log_path = $config->get('log_path', JPATH_ROOT . "/log" );
	$file = $log_path . "/" . VmConfig::$logFileName . VmConfig::LOGFILEEXT;

	if (!class_exists ('Permissions')) {
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
	}
	if(Permissions::getInstance()->check('admin')){
		$show_error_msg = TRUE;
	} else {
		$show_error_msg = FALSE;
	}

	if (!is_dir($log_path)) {
		jimport('joomla.filesystem.folder');
		if (!JFolder::create($log_path)) {
			if ($show_error_msg){
				$msg = 'Could not create path ' . $log_path . ' to store log information. Check your folder ' . $log_path . ' permissions.';
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'error');
			}
			return;
		}
	}
	if (!is_writable($log_path)) {
		if ($show_error_msg){
			$msg = 'Path ' . $log_path . ' to store log information is not writable. Check your folder ' . $log_path . ' permissions.';
			$app = JFactory::getApplication();
			$app->enqueueMessage($msg, 'error');
		}
		return;
	}

	// Initialise variables.
	$FTPOptions = JClientHelper::getCredentials('ftp');
	$head = false;
	$fsize = false;
	$amount = 32768;
	$offset = 0;
	if (!JFile::exists($file)) {
		// blank line to prevent information disclose: https://bugs.php.net/bug.php?id=60677
		// from Joomla log file
		$head = "#\n";
		$head .= '#<?php die("Forbidden."); ?>'."\n";

	} else {
		$fsize = @ filesize($file);

		if($FTPOptions['enabled']){
			$maxSizeLogFile = 32768;	//32kb
		} else {
			$maxSizeLogFile = 524288;//1048576;	//1MB
		}
		if($fsize and $fsize>$maxSizeLogFile){
			$disk_free_space = disk_free_space($log_path);
			if($disk_free_space<VmConfig::get('minHDD',67108864)){

				$offset= $maxSizeLogFile/4;

			} else {
				$fileRename = $log_path . "/" . VmConfig::$logFileName.'_'.JFactory::getDate()->toFormat ('%Y-%m-%d_%H-%M') . VmConfig::LOGFILEEXT;
				JFile::move($file,$fileRename);
				$head = "#\n";
				$head .= '#<?php die("Forbidden."); ?>'."\n";
			}
		}
	}


	if ($FTPOptions['enabled'] == 0){
		static $fp;


		$fp = fopen ($file, 'a+');

		if(!empty($offset)){
			//not a good solution yet, we just delete the ending and add the other stuff again.
			ftruncate($fp,$offset);
		}
		if ($fp) {
			if ($head) {
				fwrite ($fp,  $head);
			}

			fwrite ($fp, "\n" . JFactory::getDate()->toFormat ('%Y-%m-%d %H:%M:%S'));
			fwrite ($fp,  " ".strtoupper($type) . ' ' . $text);
			fclose ($fp);
		} else {
			if ($show_error_msg){
				$msg = 'Could not write in file  ' . $file . ' to store log information. Check your file ' . $file . ' permissions.';
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'error');
			}
		}
	} else {

		$buffer = JFile::read($file,false,$amount,8192,$offset);
		if ($head) {
			$buffer .= $head;
		}
		//This can make trouble if people use FTP and get a lot errors. We strongly recommened to get a hosting which works without the FTP help construction
		$buffer .= "\n" .  JFactory::getDate()->toFormat('%Y-%m-%d %H:%M:%S');
		$buffer .= " " . strtoupper($type) . ' ' . $text;
		if (!JFile::write($file, $buffer)) {
			if ($show_error_msg){
				$msg = 'Could not write in file  ' . $file . ' to store log information. Check your file ' . $file . ' permissions.';
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'error');
			}
			return;
		}
	}


	return;

}


/**
 * The time how long the config in the session is valid.
 * While configuring the store, you should lower the time to 10 seconds.
 * Later in a big store it maybe useful to rise this time up to 1 hr.
 * That would mean that changing something in the config can take up to 1 hour until this change is effecting the shoppers.
 */

/**
 * We use this Class STATIC not dynamically !
 */
class VmConfig {

	// instance of class
	private static $_jpConfig = NULL;
	public static $_debug = NULL;
	public static $_starttime = array();
	public static $loaded = FALSE;

	public static $maxMessageCount = 0;
	public static $maxMessage = 100;
	public static $echoDebug = FALSE;
	public static $logDebug = FALSE;
	public static $logFileName = 'com_virtuemart';
	const LOGFILEEXT = '.log.php';

	public static $lang = FALSE;
	public static $vmlang = FALSE;
	public static $langTag = FALSE;
	public static $vmlangTag = FALSE;
	public static $langCount = 0;

	var $_params = array();
	var $_raw = array();


	private function __construct() {

		if(function_exists('mb_ereg_replace')){
			mb_regex_encoding('UTF-8');
			mb_internal_encoding('UTF-8');
		}

		//if(ini_get('precision')!=15){
		ini_set('precision', 15);	//We need at least 20 for correct precision if json is using a bigInt ids
		//But 17 has the best precision, using higher precision adds fantasy numbers to the end
		//}


	}

	static function getStartTime(){
		return self::$_starttime;
	}

	static function setStartTime($name,$value){
		self::$_starttime[$name] = $value;
	}

	static function showDebug(){

		if(self::$_debug===NULL){

			$debug = VmConfig::get('debug_enable','none');
			//$debug = 'all';	//this is only needed, when you want to debug THIS file
			// 1 show debug only to admins
			if($debug === 'admin' ){
				if (!class_exists ('Permissions')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
				}
				if(Permissions::getInstance()->check('admin')){
					self::$_debug = TRUE;
				} else {
					self::$_debug = FALSE;
				}
			}
			// 2 show debug to anyone
			else {
				if ($debug === 'all') {
					self::$_debug = TRUE;
				}
				// else dont show debug
				else {
					self::$_debug = FALSE;
				}
			}

			if(self::$_debug){
				ini_set('display_errors', '1');
				//error_reporting(E_ALL ^ E_STRICT);
			} else {
				ini_set('display_errors', '0');
				if(version_compare(phpversion(),'5.4.0','<' )){
					error_reporting( E_ALL & ~E_STRICT );
				} else {
					error_reporting( E_ALL ^ E_STRICT );
				}
			}
		}
		//$nu = $ha;
		return self::$_debug;
	}

	/**
	 * Ensures a certain Memory limit for php (if server supports it)
	 * @author Max Milbers
	 * @param int $minMemory
	 */
	static function ensureMemoryLimit($minMemory=0){

		if($minMemory === 0) $minMemory = VmConfig::get('minMemory','128M');
		$memory_limit = VmConfig::getMemoryLimit();

		if($memory_limit<$minMemory)  @ini_set( 'memory_limit', $minMemory.'M' );

	}

	/**
	 * Returns the PHP memory limit of the server in MB, regardless the used unit
	 * @author Max Milbers
	 * @return float|int PHP memory limit in MB
	 */
	static function getMemoryLimit(){

		$iniValue = ini_get('memory_limit');

		if($iniValue<=0) return 2048;	//We assume 2048MB as unlimited setting
		$iniValue = strtoupper($iniValue);
		if(strpos($iniValue,'M')!==FALSE){
			$memory_limit = (int) substr($iniValue,0,-1);
		} else if(strpos($iniValue,'K')!==FALSE){
			$memory_limit = (int) substr($iniValue,0,-1) / 1024.0;
		} else if(strpos($iniValue,'G')!==FALSE){
			$memory_limit = (int) substr($iniValue,0,-1) * 1024.0;
		} else {
			$memory_limit = (int) $iniValue / 1048576.0;
		}
		return $memory_limit;
	}

	static function ensureExecutionTime($minTime=0){

		if($minTime === 0) $minTime = (int) VmConfig::get('minTime',120);
		$max_execution_time = self::getExecutionTime();
		if((int)$max_execution_time<$minTime) {
			@ini_set( 'max_execution_time', $minTime );
		}
	}

	static function getExecutionTime(){
		$max_execution_time = (int) ini_get('max_execution_time');
		if(empty($max_execution_time)){
			$max_execution_time = (int) VmConfig::get('minTime',120);
		}
		return $max_execution_time;
	}

	/**
	 * loads a language file, the trick for us is that always the config option enableEnglish is tested
	 * and the path are already set and the correct order is used
	 * We use first the english language, then the default
	 *
	 * @author Max Milbers
	 * @static
	 * @param $name
	 * @return bool
	 */
	static public function loadJLang($name,$site=false,$tag=0){

		$jlang =JFactory::getLanguage();
		if(empty($tag))$tag = $jlang->getTag();

		$path = $basePath = JPATH_VM_ADMINISTRATOR;
		if($site){
			$path = $basePath = JPATH_VM_SITE;
		}

		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			$testpath = $basePath.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$name.'.ini';
			if(!file_exists($testpath)){
				$epath = JPATH_ADMINISTRATOR;
				if($site){
					$epath = JPATH_SITE;
				}
			} else {
				$epath = $path;
			}
			$jlang->load($name, $epath, 'en-GB');
		}

		$testpath = $basePath.DS.'language'.DS.$tag.DS.$tag.'.'.$name.'.ini';
		if(!file_exists($testpath)){
			$path = JPATH_ADMINISTRATOR;
			if($site){
				$path = JPATH_SITE;
			}
		}

		$jlang->load($name, $path,$tag,true);

		return $jlang;
	}

	/**
	 * @static
	 * @author Valerie Isaksen
	 * @param $name
	 */
	static public function loadModJLang($name){

		$jlang =JFactory::getLanguage();
		$tag = $jlang->getTag();

		$path = $basePath = JPATH_VM_MODULES.DS.$name;

		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			$testpath = $basePath.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$name.'.ini';
			if(!file_exists($testpath)){
				$path = JPATH_ADMINISTRATOR;
			}
			$jlang->load($name, $path, 'en-GB');
		}

		$testpath = $basePath.DS.'language'.DS.$tag.DS.$tag.'.'.$name.'.ini';
		if(!file_exists($testpath)){
			$path = JPATH_ADMINISTRATOR;
		}

		$jlang->load($name, $path,$tag,true);
	}

	/**
	 * Loads the configuration and works as singleton therefore called static. The call using the program cache
	 * is 10 times faster then taking from the session. The session is still approx. 30 times faster then using the file.
	 * The db is 10 times slower then the session.
	 *
	 * Performance:
	 *
	 * Fastest is
	 * Program Cache: 1.5974044799805E-5
	 * Session Cache: 0.00016094612121582
	 *
	 * First config db load: 0.00052118301391602
	 * Parsed and in session: 0.001554012298584
	 *
	 * After install from file: 0.0040450096130371
	 * Parsed and in session: 0.0051419734954834
	 *
	 *
	 * Functions tests if already loaded in program cache, session cache, database and at last the file.
	 *
	 * Load the configuration values from the database into a session variable.
	 * This step is done to prevent accessing the database for every configuration variable lookup.
	 *
	 * @author Max Milbers
	 * @param $force boolean Forces the function to load the config from the db
	 */
	static public function loadConfig($force = FALSE,$fresh = FALSE) {

		if($fresh){
			return self::$_jpConfig = new VmConfig();
		}
		vmSetStartTime('loadConfig');
		if(!$force){
			if(!empty(self::$_jpConfig) && !empty(self::$_jpConfig->_params)){

				return self::$_jpConfig;
			}
		}

		self::$_jpConfig = new VmConfig();

		if(!class_exists('VirtueMartModelConfig')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'config.php');
		$configTable  = VirtueMartModelConfig::checkConfigTableExists();

		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		$freshInstall = vRequest::getInt('install',false);
		if(empty($configTable) or $freshInstall){
			if(!$freshInstall){
				$installed = VirtueMartModelConfig::checkVirtuemartInstalled();
				if(!$installed){

					$jlang =JFactory::getLanguage();
					$selectedLang = $jlang->getTag();

					if(empty($selectedLang)){
						$selectedLang = $jlang->setLanguage($selectedLang);
					}

					$msg = '';
					$q = 'SELECT `element` FROM `#__extensions` WHERE type = "language" and enabled = "1"';
					$db->setQuery($q);
					$knownLangs = $db->loadColumn();
					//vmdebug('Selected language '.$selectedLang.' $knownLangs ',$knownLangs);
					if($app->isAdmin() and !in_array($selectedLang,$knownLangs)){
						$link = 'index.php?option=com_installer&view=languages';
						$msg = 'Install your selected language <b>'.$selectedLang.'</b> first in <a href="'.$link.'">joomla language manager</a>, just select then the component VirtueMart under menu "component", to proceed with the installation ';
						$app->enqueueMessage($msg);
					}
					//else {
						if($app->isSite()){
							$link = 'index.php?option=com_virtuemart';
						} else {
							$link = 'index.php?option=com_virtuemart&view=updatesmigration&install=1';
							$msg = 'Install Virtuemart first, click on the menu component and select VirtueMart';
						}
						if($app->isSite()){
							$link = JURI::root(true).'/administrator/'.$link;
						}
						$app->redirect($link,$msg);
					//}
				}
				if($installed){
					self::$_jpConfig->installVMconfig();
				}
			} else {
				self::$_jpConfig->installVMconfig($freshInstall);
			}
		}


		$install = 'no';

		if(empty(self::$_jpConfig->_raw)){
			$query = ' SELECT `config` FROM `#__virtuemart_configs` WHERE `virtuemart_config_id` = "1";';
			$db->setQuery($query);
			self::$_jpConfig->_raw = $db->loadResult();
			if(empty(self::$_jpConfig->_raw)){
				if(self::installVMconfig($freshInstall)){
					$install = 'yes';
					$db->setQuery($query);
					self::$_jpConfig->_raw = $db->loadResult();
					self::$_jpConfig->_params = NULL;
				} else {
					$app ->enqueueMessage('Error loading configuration file','Error loading configuration file, please contact the storeowner');
				}
			}
		}

		$i = 0;

		$pair = array();
		if (!empty(self::$_jpConfig->_raw)) {
			$config = explode('|', self::$_jpConfig->_raw);
			foreach($config as $item){
				$item = explode('=',$item);
				if(!empty($item[1])){
					// if($item[0]!=='offline_message' && $item[0]!=='dateformat' ){
					if($item[0]!=='offline_message' ){
						try {
							$value = @unserialize($item[1] );

							if($value===FALSE){
								$app ->enqueueMessage('Exception in loadConfig for unserialize '.$item[0]. ' '.$item[1]);
								$uri = JFactory::getURI();
								$configlink = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=config';
								$app ->enqueueMessage('To avoid this message, enter your virtuemart <a href="'.$configlink.'">config</a> and just save it one time');
							} else {
								$pair[$item[0]] = $value;
							}
						}catch (Exception $e) {
							vmdebug('Exception in loadConfig for unserialize '. $e->getMessage(),$item);
						}
					} else {
						$pair[$item[0]] = unserialize(base64_decode($item[1]) );
					}

				} else {
					$pair[$item[0]] ='';
				}

			}

// 			$pair['sctime'] = microtime(true);
			self::$_jpConfig->_params = $pair;

			self::$_jpConfig->_params['sctime'] = microtime(TRUE);
			//self::$_jpConfig->set('sctime',microtime(TRUE));
			//self::setdbLanguageTag();
			self::$_jpConfig->_params['vmlang'] = self::setdbLanguageTag();

			vmTime('loadConfig db '.$install,'loadConfig');

			return self::$_jpConfig;
		}


		$app ->enqueueMessage('Attention config is empty');
		return self::$_jpConfig;
	}


	/*
	 * Set defaut language tag for translatable table
	 *
	 * @author Max Milbers
	 * @return string valid langtag
	 */
	static public function setdbLanguageTag() {

		if (self::$lang) {
			return self::$lang;
		}

		$langs = (array)self::get('active_languages',array());
		self::$langCount = count($langs);
		$siteLang = JRequest::getString('vmlang',FALSE );
		//vmdebug('My $siteLang by JRequest::getString("vmlang",JRequest::getString("lang")) '.$siteLang);
		$params = JComponentHelper::getParams('com_languages');
		$defaultLang = $params->get('site', 'en-GB');//use default joomla

		if( JFactory::getApplication()->isSite()){
			if (!$siteLang) {
				if ( JVM_VERSION===1 ) {
					// try to find in session lang
					// this work with joomfish j1.5 (application.data.lang)
					$session  =JFactory::getSession();
					$registry = $session->get('registry');
					$siteLang = $registry->getValue('application.data.lang') ;
				} else  {
					jimport('joomla.language.helper');
					$siteLang = JFactory::getLanguage()->getTag();
					vmdebug('My selected language by JFactory::getLanguage()->getTag() '.$siteLang);
				}
			}
		} else {
			if(!$siteLang){
				$siteLang = $defaultLang;
			}
		}

		if(!in_array($siteLang, $langs)) {
			if(count($langs)===0){
				$siteLang = $defaultLang;
			} else {
				$siteLang = $langs[0];
			}
		}
		self::$vmlangTag = self::$langTag = $siteLang;
		self::$vmlang = self::$lang = strtolower(strtr($siteLang,'-','_'));
		vmdebug('$siteLang: '.$siteLang.' self::$_jpConfig->lang '.self::$lang);
		defined('VMLANG') or define('VMLANG', self::$lang );

		return self::$lang;
	}

	/**
	 * Find the configuration value for a given key
	 *
	 * @author Max Milbers
	 * @param string $key Key name to lookup
	 * @return Value for the given key name
	 */
	static function get($key, $default='',$allow_load=FALSE)
	{

		$value = '';
		if ($key) {

			if (empty(self::$_jpConfig->_params) && $allow_load) {
				self::loadConfig();
			}

			if (!empty(self::$_jpConfig->_params)) {
				if(array_key_exists($key,self::$_jpConfig->_params) && isset(self::$_jpConfig->_params[$key])){
					$value = self::$_jpConfig->_params[$key];
				} else {
					$value = $default;
				}

			} else {
				$value = $default;
			}

		} else {
			$app = JFactory::getApplication();
			$app -> enqueueMessage('VmConfig get, empty key given');
		}

		return $value;
	}

	static function set($key, $value){

		if (empty(self::$_jpConfig->_params)) {
			self::loadConfig();
		}

		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if(Permissions::getInstance()->check('admin')){
			if (!empty(self::$_jpConfig->_params)) {
				self::$_jpConfig->_params[$key] = $value;
			}
		}

	}

	/**
	 * For setting params, needs assoc array
	 * @author Max Milbers
	 */
	function setParams($params,$replace=FALSE){

		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if(Permissions::getInstance()->check('admin')){
			//The idea with the merge was that 3rd party use the config to store stuff there,
			//But we doubt that anyone does it, because the vm team itself never uses it.
			//To avoid errors like unserialize hidemainmenu b:0;, we just replace now the config with the data,
			//Hmm does not work, because people may use config values, not in the config form
			unset($this->_params['hidemainmenu']);
			unset($this->_params['pdf_invoice']); // parameter remove and replaced by inv_os
			unset($this->_params['list_limit']);
			unset($this->_params['pagination_sequence']);
			if($replace){
				self::$_jpConfig->_params = $params;
			} else {
				self::$_jpConfig->_params = array_merge($this->_params,$params);
			}

			//self::$_jpConfig->_params = $params;
		}

	}

	/**
	 * Writes the params as string and escape them before
	 * @author Max Milbers
	 */
	function toString(){
		$raw = '';
		$db = JFactory::getDBO();

		jimport( 'joomla.utilities.arrayhelper' );
		foreach(self::$_jpConfig->_params as $paramkey => $value){

			//Texts get broken, when serialized, therefore we do a simple encoding,
			//btw we need serialize for storing arrays   note by Max Milbers
//			if($paramkey!=='offline_message' && $paramkey!=='dateformat'){
			if($paramkey!=='offline_message'){
				$raw .= $paramkey.'='.serialize($value).'|';
			} else {
				$raw .= $paramkey.'='.base64_encode(serialize($value)).'|';
			}
		}
		self::$_jpConfig->_raw = substr($raw,0,-1);
		return self::$_jpConfig->_raw;
	}

	/**
	 * Find the currenlty installed version
	 *
	 * @author RickG
	 * @param boolean $includeDevStatus True to include the development status
	 * @return String of the currently installed version
	 */
	static function getInstalledVersion($includeDevStatus=FALSE)
	{
		// Get the installed version from the wmVersion class.

		return vmVersion::$RELEASE;
	}

	/**
	 * Return if the used joomla function is j15
	 * @deprecated use JVM_VERSION instead
	 */
	function isJ15(){
		return (strpos(JVERSION,'1.5') === 0);
	}


	function getCreateConfigTableQuery(){

		return "CREATE TABLE IF NOT EXISTS `#__virtuemart_configs` (
  `virtuemart_config_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `config` text,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`virtuemart_config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Holds configuration settings' AUTO_INCREMENT=1 ;";
	}

	/**
	 * Read the file vm_config.dat from the install directory, compose the SQL to write
	 * the config record and store it to the dabase.
	 *
	 * @param $_section Section from the virtuemart_defaults.cfg file to be parsed. Currently, only 'config' is implemented
	 * @return Boolean; true on success, false otherwise
	 * @author Oscar van Eijk
	 */
	public function installVMconfig($freshInstall=false){

		$_value = self::readConfigFile(FALSE,$freshInstall);

		if (!$_value) {
			return FALSE;
		}
		$_value = join('|', $_value);
		self::$_jpConfig->_raw = $_value;

		if($freshInstall){
			return true;
		}

		$qry = self::$_jpConfig->getCreateConfigTableQuery();
		$_db = JFactory::getDBO();
		$_db->setQuery($qry);
		$_db->query();

		$query = 'SELECT `virtuemart_config_id` FROM `#__virtuemart_configs`
						 WHERE `virtuemart_config_id` = 1';
		$_db->setQuery( $query );
		if ($_db->query()){
			$qry = 'DELETE FROM `#__virtuemart_configs` WHERE `virtuemart_config_id`=1';
			$_db->setQuery($qry);
			$_db->query();
		}

		$qry = "INSERT INTO `#__virtuemart_configs` (`virtuemart_config_id`, `config`) VALUES ('1', '$_value')";

		if(!$freshInstall){
			$_db->setQuery($qry);
			if (!$_db->query()) {
				JError::raiseWarning(1, 'VmConfig::installVMConfig: '.JText::_('COM_VIRTUEMART_SQL_ERROR').' '.$_db->stderr(TRUE));
				echo 'VmConfig::installVMConfig: '.JText::_('COM_VIRTUEMART_SQL_ERROR').' '.$_db->stderr(TRUE);
				die;
			}else {
				//vmdebug('Config installed file, store values '.$_value);
				return TRUE;
			}
		} else {
			return false;
		}


	}

	/**
	 * We should this move out of this file, because it is usually only used one time in a shop life
	 * @author Oscar van Eijk
	 * @author Max Milbers
	 */
	static function readConfigFile($returnDangerousTools,$freshInstall = false){

		$_datafile = JPATH_VM_ADMINISTRATOR.DS.'virtuemart.cfg';
		if (!file_exists($_datafile)) {
			if (file_exists(JPATH_VM_ADMINISTRATOR.DS.'virtuemart_defaults.cfg-dist')) {
				if (!class_exists ('JFile')) {
					require(JPATH_VM_LIBRARIES . DS . 'joomla' . DS . 'filesystem' . DS . 'file.php');
				}
				JFile::copy('virtuemart_defaults.cfg-dist','virtuemart.cfg',JPATH_VM_ADMINISTRATOR);
			} else {
				JError::raiseWarning(500, 'The data file with the default configuration could not be found. You must configure the shop manually.');
				return FALSE;
			}

		} else {
			vmInfo('Taking config from file');
			//vmTrace('read config file, why?',TRUE);
		}

		$_section = '[CONFIG]';
		$_data = fopen($_datafile, 'r');
		$_configData = array();
		$_switch = FALSE;
		while ($_line = fgets ($_data)) {
			$_line = trim($_line);

			if (strpos($_line, '#') === 0) {
				continue; // Commentline
			}
			if ($_line == '') {
				continue; // Empty line
			}
			if (strpos($_line, '[') === 0) {
				// New section, check if it's what we want
				if (strtoupper($_line) == $_section) {
					$_switch = TRUE; // Ok, right section
				} else {
					$_switch = FALSE;
				}
				continue;
			}
			if (!$_switch) {
				continue; // Outside a section or inside the wrong one.
			}

			if (strpos($_line, '=') !== FALSE) {

				$pair = explode('=',$_line);
				if(isset($pair[1])){
					if(strpos($pair[1], 'array:') !== FALSE){
						$pair[1] = substr($pair[1],6);
						$pair[1] = explode('|',$pair[1]);
					}
					// if($pair[0]!=='offline_message' && $pair[0]!=='dateformat'){
					if($pair[0]!=='offline_message'){
						$_line = $pair[0].'='.serialize($pair[1]);
					} else {
						$_line = $pair[0].'='.base64_encode(serialize($pair[1]));
					}

					if(($freshInstall or $returnDangerousTools) && $pair[0] == 'dangeroustools' ){

						if($returnDangerousTools){
							if ($pair[1] == "0") {
								return FALSE;
							}
							else {
								return TRUE;
							}
						}
						if($freshInstall){
							vmdebug('$freshInstall');
							$pair[1]="1";
							$_line = $pair[0].'='.serialize($pair[1]);
						}
						vmdebug('dangeroustools '.$pair[1]);
					}

				} else {
					$_line = $pair[0].'=';
				}
				$_configData[] = $_line;

			}

		}

		fclose ($_data);

		if (!$_configData) {
			return FALSE; // Nothing to do
		} else {
			return $_configData;
		}
	}

}

class vmURI{

	static function getCleanUrl ($JURIInstance = 0,$parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment')) {

		if(!class_exists('JFilterInput')) require (JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filter'.DS.'input.php');
		$_filter = JFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1);
		if($JURIInstance===0)$JURIInstance = JURI::getInstance();
		return $_filter->clean($JURIInstance->toString($parts));
	}
}

// pure php no closing tag
