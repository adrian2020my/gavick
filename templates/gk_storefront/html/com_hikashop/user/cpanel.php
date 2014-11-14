<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="hikashop_cpanel_main" id="hikashop_cpanel_main">
	<h1><?php echo JText::_('CUSTOMER_ACCOUNT');?></h1>
	
	<div class="hikashopcpanel" id="hikashopcpanel">
		<?php
		foreach($this->buttons as $oneButton){
			$url = hikashop_level($oneButton['level']) ? 'onclick="document.location.href=\''.$oneButton['link'].'\';"' : ''; ?>
			<div <?php echo $url; ?> class="icon hikashop_cpanel_icon_div">
				<a href="<?php echo hikashop_level($oneButton['level']) ? $oneButton['link'] : '#'; ?>">
					<?php echo $oneButton['description']; ?>
				</a>
			</div>
		<?php }	?>
	</div>
</div>
