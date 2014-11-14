<?php
/**
 *
 * @version $Id: virtuemart.php 6246 2012-07-09 19:00:20Z Milbo $
 * @package VirtueMart
 * @subpackage core
 * @copyright Copyright (C) VirtueMart Team - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) {
	$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php';
	if(file_exists($path)){
		require($path);
		VmConfig::loadConfig();
	} else {
		$app = JFactory::getApplication();
		$app->enqueueMessage('VirtueMart Core is not installed, please install VirtueMart again, or uninstall the AIO component by the joomla extension manager');
		return false;
	}
}

$task = vRequest::getCmd('task');
if($task=='updateDatabase'){
	vRequest::vmCheckToken('Invalid Token, in ' . vRequest::getCmd('task')) ;

	//Update Tables
	if(!class_exists('Permissions'))
	require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart' . DS . 'helpers' . DS . 'permissions.php');
	if(!Permissions::getInstance()->check('admin')){
		$msg = 'Forget IT';
		$this->setRedirect('index.php?option=com_virtuemart_allinone', $msg);
	} else {
		if(!class_exists('com_virtuemart_allinoneInstallerScript')) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart_allinone'.DS.'script.vmallinone.php');
		$updater = new com_virtuemart_allinoneInstallerScript();
		$updater->vmInstall();
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_virtuemart_allinone', 'Database updated');
	}

}

?>
<script type="text/javascript">
	<!--
	function confirmation(message, destnUrl) {
		var answer = confirm(message);
		if (answer) {
			window.location = destnUrl;
		}
	}
	//-->
</script>

<table>

	<tr>
		<td align="center">
			<?php
			VmConfig::loadConfig();
			VmConfig::loadJLang('com_virtuemart');

			?>
			<?php $link=JROUTE::_('index.php?option=com_virtuemart_allinone&task=updateDatabase&'.JSession::getFormToken().'=1' ); ?>
			<button onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_UPDATE_VMPLUGINTABLES') ); ?>', '<?php echo $link; ?>');">

				<?php echo JText::_('COM_VIRTUEMART_UPDATE_VMPLUGINTABLES'); ?>
			</button>
		</td>
	</tr>
</table>


