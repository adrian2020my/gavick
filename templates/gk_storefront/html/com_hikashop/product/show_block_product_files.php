<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikashop_product_files_main" class="hikashop_product_files_main">
	<?php
	if (!empty ($this->element->files)) {
		$skip = true;
		foreach ($this->element->files as $file) {
			if ($file->file_free_download)
				$skip = false;
		}
		if (!$skip) {
			global $Itemid;
			$url_itemid='';
			if(!empty($Itemid)){
				$url_itemid='&Itemid='.$Itemid;
			}
		?>
			<?php
			$html = array ();
			echo '<ul>';
			echo '<li><strong>' . JText :: _('DOWNLOADS') . '</strong></li>';
			foreach ($this->element->files as $file) {
				if (empty ($file->file_name)) {
					$file->file_name = $file->file_path;
				}
				$fileHtml = '';
				if (!empty ($file->file_free_download)) {
					$fileHtml = '<li><a class="hikashop_product_file_link" href="' . hikashop_completeLink('product&task=download&file_id=' . $file->file_id.$url_itemid) . '">' . $file->file_name . '</a></li>';
				}
				$html[] = $fileHtml;
			}
			echo implode('', $html);
			echo '</ul>';
			?>
			<?php
		}
	}
	?>
</div>
