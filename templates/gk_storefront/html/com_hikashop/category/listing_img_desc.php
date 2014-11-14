<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

$link = $this->getLink($this->row);

?>
<div>
	<a href="<?php echo $link;?>" title="<?php echo $this->escape($this->row->category_name); ?>">
		<?php
		$image_options = array('default' => true,'forcesize'=>$this->config->get('image_force_size',true),'scale'=>$this->config->get('image_scale_mode','inside'));
		$img = $this->image->getThumbnail(@$this->row->file_path, array('width' => $this->image->main_thumbnail_x, 'height' => $this->image->main_thumbnail_y), $image_options);
		if($img->success) {
			echo '<img class="hikashop_product_listing_image" title="'.$this->escape(@$this->row->file_description).'" alt="'.$this->escape(@$this->row->file_name).'" src="'.$img->url.'"/>';
		}
		?>
	</a>
</div>

<span class="hikashop_category_desc" style="text-align:<?php echo $this->align; ?>;">
	<?php
	echo preg_replace('#<hr *id="system-readmore" */>.*#is','',$this->row->category_description);
	?>
</span>