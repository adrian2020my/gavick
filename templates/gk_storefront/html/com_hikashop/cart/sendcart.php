<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikashop_product_contact_<?php echo JRequest::getInt('cid');?>_page" class="hikashop_product_contact_page">
	<div class="toolbar" id="toolbar" style="float: right;">
		<button class="button" type="button" onclick="submitform('send_email');"><?php echo JText::_('OK'); ?></button>
		<button class="button" type="button" onclick="history.back();"><?php echo JText::_('HIKA_CANCEL'); ?></button>
	</div>
	
	<form action="<?php echo hikashop_completeLink('product'); ?>" method="post"  name="adminForm" id="adminForm">
		<table>
			<tr>
				<td class="key">
					<label for="data[register][email]">
						<?php echo JText::_( 'HIKA_RECIPIENTS' ); ?>
					</label>
				</td>
				<td>
					<input type="text" name="data[register][email]" size="40" value="<?php echo $this->escape(@$this->element->email);?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="data[register][altbody]">
						<?php echo JText::_( 'ADDITIONAL_TEXT' ); ?> 
					</label>
				</td>
				<td>
					<textarea cols="60" rows="10" name="data[register][altbody]"></textarea>
				</td>
			</tr>
		</table>
		<input type="hidden" name="data[register][product_id]" value="<?php echo JRequest::getInt('cid');?>" />
		<input type="hidden" name="cid" value="<?php echo JRequest::getInt('cid');?>" />
		<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="ctrl" value="product" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
