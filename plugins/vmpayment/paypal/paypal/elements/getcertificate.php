<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id:$
 */

class JElementGetcertificate extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Getcertificate';


		function fetchElement ($name, $value, &$node, $control_name) {

			jimport ('joomla.filesystem.folder');
			jimport ('joomla.filesystem.file');
			$lang = JFactory::getLanguage ();
			$lang->load ('com_virtuemart', JPATH_ADMINISTRATOR);
			// path to images directory
			$folder = $node->attributes ('directory');
			$safePath = VmConfig::get ('forSale_path', '');

			$certificatePath=$safePath.$folder;
			$certificatePath = JPath::clean($certificatePath);
			$class = ($node->attributes('class') ? 'class="' . $node->attributes('class') . '"' : '');

			// Is the path a folder?
			if (!is_dir($certificatePath)){
				return '<span '.$class.'>'.vmText::sprintf ('VMPAYMENT_PAYPAL_CERTIFICATE_FOLDER_NOT_EXIST', $certificatePath).'</span>';
			}

			$path = str_replace ('/', DS, $certificatePath);
			$filter = $node->attributes ('filter');
			$exclude = array($node->attributes ('exclude'), '.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html');
			$pattern = implode ( "|", $exclude);
			$stripExt = $node->attributes ('stripext');

			$files = JFolder::files ($path, $filter, FALSE, FALSE, $exclude);

			$options = array();

			if (is_array ($files)) {
				foreach ($files as $file) {
					if ($exclude) {
						if (preg_match (chr (1) . $pattern . chr (1), $file)) {
							continue;
						}
					}
					if ($stripExt) {
						$file = JFile::stripExt ($file);
					}
					$options[] = JHTML::_ ('select.option', $file, $file);
				}
			}
			$class .= ' size="5" data-placeholder="'.vmText::_('COM_VIRTUEMART_DRDOWN_SELECT_SOME_OPTIONS').'"';
			return JHTML::_ ('select.genericlist', $options, '' . $control_name . '[' . $name . ']', $class, 'value', 'text', $value, $control_name . $name);
		}

}