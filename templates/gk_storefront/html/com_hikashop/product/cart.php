<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

global $Itemid;

$url_itemid = '';
if (!empty($Itemid)) {
    $url_itemid = '&Itemid=' . $Itemid;
}
$itemid_for_checkout = $this->config->get('checkout_itemid','0');
if(!empty($itemid_for_checkout)){
	$url_checkout = hikashop_completeLink('checkout&Itemid='.$itemid_for_checkout);
}else{
	$url_checkout = hikashop_completeLink('checkout'.$url_itemid);
}

$this->setLayout('listing_price');
$this->params->set('show_quantity_field', 0);
$desc      = $this->params->get('msg');
$cart_type = $this->params->get('cart_type', 'cart');

if ($cart_type == 'wishlist') {
    $convertText    = JText::_('WISHLIST_TO_CART');
    $displayText    = JText::_('DISPLAY_THE_WISHLIST');
    $displayAllText = JText::_('DISPLAY_THE_WISHLISTS');
    $emptyText      = JText::_('WISHLIST_EMPTY');
} else {
    $convertText    = JText::_('CART_TO_WISHLIST');
    $displayText    = JText::_('DISPLAY_THE_CART');
    $displayAllText = JText::_('DISPLAY_THE_CARTS');
    $emptyText      = JText::_('CART_EMPTY');
}

if (empty($desc) && $desc != '0') {
    $this->params->set('msg', $emptyText);
}

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

$cart_type = $this->params->get('cart_type', 'cart');

if ($this->params->get('from', 'no') == 'no') {
    $this->params->set('from', JRequest::getString('from', 'display'));
}

if (empty($this->rows)) {
    $desc = trim($this->params->get('msg'));
    if (!empty($desc) || $desc == '0') {
        echo $this->notice_html;
?>
		<div id="hikashop_cart" class="hikashop_cart">
			<?php echo $desc; ?>
		</div>
<?php
    }
} else {
?>
<div id="hikashop_cart" class="hikashop_cart">
<?php
    if ($this->params->get('from', 'display') != 'module') {
        echo '<div class="hikashop_product_cart_links">';
        echo '<div class="hikashop_product_cart_show_carts_link">';
        echo $this->cart->displayButton($displayAllText, 'cart', $this->params, hikashop_completeLink('cart&task=showcarts&cart_type=' . $cart_type . $url_itemid), '');
        echo '</div>';
        echo '</div>';
    }
    
    echo $this->notice_html;
    $row_count = 0;
    
    if (JRequest::getCmd('tmpl') == 'json' || JRequest::getCmd('hikashop_ajax') == 1 || $this->params->get('small_cart')) {
        $this->row = $this->total;
        if ($this->params->get('show_cart_quantity', 1)) {
            $qty   = 0;
            $group = $this->config->get('group_options', 0);
            foreach ($this->rows as $i => $row) {
                if (empty($row->cart_product_quantity) && $cart_type != 'wishlist')
                    continue;
                if ($group && $row->cart_product_option_parent_id)
                    continue;
                $qty += $row->cart_product_quantity;
            }
            $text = JText::sprintf('X_ITEMS_FOR_X', $qty, $this->loadTemplate());
        } else {
            $text = JText::sprintf('TOTAL_IN_CART_X', $this->loadTemplate());
        }
        
        if ($cart_type != 'wishlist') : ?>
			<span class="hikashop_small_cart_total_title"><?php echo str_replace('span', 'strong', $text); ?></span>
            <?php
        else :
            foreach ($this->rows as $row) {
                $cart_id = $row->cart_id;
            }
?>
			<span class="hikashop_small_cart_total_title"><?php echo str_replace('span', 'strong', $text); ?></span>
			<?php
       endif;
    } else {
        $form = $cart_type == 'wishlist' ? 'hikashop_wishlist_form' : 'hikashop_cart_form';
?>
	<h3><span><?php echo JText::_('TPL_GK_LANG_MY_CART'); ?></span></h3>

	<form action="<?php echo hikashop_completeLink('product&task=updatecart' . $url_itemid, false, true); ?>" method="post" name="<?php echo $form; ?>">
		<div>
			<div>
				<?php
        $k                        = 0;
        $this->cart_product_price = true;
        $group                    = $this->config->get('group_options', 0);
        $cart_id                  = 0;
        $app                      = JFactory::getApplication();
        $productClass             = hikashop_get('class.product');
        
        $defaultParams = $this->config->get('default_params');
        
        $this->image = hikashop_get('helper.image');
        $height      = $this->config->get('thumbnail_y');
        $width       = $this->config->get('thumbnail_x');
        foreach ($this->rows as $i => $row) {
            $cart_id = $row->cart_id;
            if (empty($row->cart_product_quantity) || @$row->hide == 1)
                continue;
            if ($group && $row->cart_product_option_parent_id)
                continue;
            $productClass->addAlias($row);
?>
						<div>
							<?php
            if (@$this->params->get('image_in_cart')) {
?>
							<div class="hikashop_cart_module_product_image hikashop_cart_value">
								<?php
                $image_options = array(
                    'default' => true,
                    'forcesize' => true,
                    'scale' => 'inside'
                );
                $img = $this->image->getThumbnail(@$row->images[0]->file_path, array(
                    'width' => 70,
                    'height' => 70
                ), $image_options);
                if ($img->success) {
                    echo '<img class="hikashop_product_cart_image" title="' . $this->escape(@$row->images[0]->file_description) . '" alt="' . $this->escape(@$row->images[0]->file_name) . '" src="' . $img->url . '"/>';
                }
?>
							</div>
							<?php
            }
            if ($this->params->get('show_cart_product_name', 1)) {
?>
				<div class="hikashop_cart_module_product_name_value hikashop_cart_value">
                
                <h3>
                	<?php if ($this->params->get('show_cart_quantity', 1)) : ?>
            		<span class="hikashop_cart_module_product_quantity_value hikashop_cart_value">
            			<?php echo $row->cart_product_quantity . '&times;'; ?>
            		</span>
                	<?php endif; ?>
                
                <?php if(@$defaultParams['link_to_product_page']) : ?> 
                	<a href="<?php echo hikashop_contentLink('product&task=show&cid='.$row->product_id.'&name='.$row->alias.$url_itemid,$row);?>">
                <?php endif ?>
               
					<?php echo $row->product_name; ?>
					<?php if ($this->config->get('show_code')) { ?>
						<span class="hikashop_product_code_cart"><?php echo $row->product_code; ?></span>
					<?php
                }
?>
				<?php if (@$defaultParams['link_to_product_page']) : ?></a><?php endif; ?>
				</h3>
				
					<div class="hikashop_cart_product_custom_item_fields">
									<?php
                if (hikashop_level(2) && !empty($this->itemFields)) {
                    foreach ($this->itemFields as $field) {
                        $namekey = $field->field_namekey;
                        if (!empty($row->$namekey) && strlen($row->$namekey)) {
                            echo '<div class="hikashop_cart_item_' . $namekey . '">' . $this->fieldsClass->getFieldName($field) . ': ' . $this->fieldsClass->show($field, $row->$namekey) . '</div>';
                        }
                    }
                }
                $input = '';
                if ($group) {
                    foreach ($this->rows as $j => $optionElement) {
                        if ($optionElement->cart_product_option_parent_id != $row->cart_product_id)
                            continue;
?>
											<div class="hikashop_cart_option_name">
												<?php echo $optionElement->product_name; ?>
											</div>
									<?php
                        $input .= 'document.getElementById(\'cart_product_option_' . $optionElement->cart_product_id . '\').value=qty_field.value;';
                        echo '<input type="hidden" id="cart_product_option_' . $optionElement->cart_product_id . '" name="item[' . $optionElement->cart_product_id . '][cart_product_quantity]" value="' . $row->cart_product_quantity . '"/>';
                    }
                }
?>

								<?php
								if ($this->params->get('show_price', 1)) : ?>
									<div class="hikashop_cart_module_product_price_value hikashop_cart_value">
									<?php
									    $this->row =& $row;
									    echo $this->loadTemplate();
									?>
									</div>
								<?php endif; ?>

								</div>
							</div>
							<?php
            }
            if ($group) {
                foreach ($this->rows as $j => $optionElement) {
                    if ($optionElement->cart_product_option_parent_id != $row->cart_product_id)
                        continue;
                    if (!empty($optionElement->prices[0])) {
                        if (!isset($row->prices[0])) {
                            $row->prices[0]->price_value          = 0;
                            $row->prices[0]->price_value_with_tax = 0;
                            $row->prices[0]->price_currency_id    = hikashop_getCurrency();
                        }
                        foreach (get_object_vars($row->prices[0]) as $key => $value) {
                            if (is_object($value)) {
                                foreach (get_object_vars($value) as $key2 => $var2) {
                                    if (strpos($key2, 'price_value') !== false)
                                        $row->prices[0]->$key->$key2 += @$optionElement->prices[0]->$key->$key2;
                                }
                            } else {
                                if (strpos($key, 'price_value') !== false)
                                    $row->prices[0]->$key += @$optionElement->prices[0]->$key;
                            }
                        }
                    }
                }
            } 
            ?>
            
            <?php
            if ($cart_type == 'wishlist' && $this->params->get('from', 'display') != 'module') : ?>
			<div class="hikashop_wishlist_display_add_to_cart">
				<?php
	                $form = ',\'hikashop_wishlist_form\'';
	                
	                $this->ajax = '
						if(qty_field == null){
							var qty_field = document.getElementById(\'hikashop_wishlist_quantity_' . $row->cart_product_id . '\').value;
						}
						if(hikashopCheckChangeForm(\'item\',\'hikashop_wishlist_form\')){
							return hikashopModifyQuantity(\'' . $this->row->product_id . '\',qty_field,1,\'hikashop_wishlist_form\',\'cart\');
						} else {
							return false;
						}
					';
	                
	                $this->setLayout('quantity');
	                echo $this->loadTemplate();
	                $this->setLayout('listing_price');
				?>
			</div>
		<?php endif; ?>
		</div>
			<?php $k = 1 - $k;
        }
        $this->cart_product_price = false; ?>
			</div>
		</div>
		
		<?php
		   if ($this->params->get('show_price', 1) && $this->params->get('cart_type', 'cart') != 'wishlist') {
		?>
					<div>
						<div>
							<?php
		            switch ($row_count) :
		                case 0:
		                case 1:
		?>
									<div class="hikashop_cart_module_product_total_value gkTotal">
										<?php
		                    $this->row = $this->total;
		                    echo $this->loadTemplate();
		?>
									</div>
									<?php
		                    break;
		                
		                default:
		?>
								<div class="hikashop_cart_module_product_total_title">
									<?php echo JText::_('HIKASHOP_TOTAL'); ?>
								</div>
				
								<div class="hikashop_cart_module_product_total_value">
								<?php
				        $this->row = $this->total;
				        echo $this->loadTemplate();
		?>
								</div>
									<?php
		                    break;
		            endswitch;
		?>
						</div>
					</div>
					<?php
		        }
		?>
		
		<?php
        if ($this->params->get('cart_type', 'cart') != 'wishlist' && $this->params->get('from', 'display') == 'module') {
            if($this->params->get('show_cart_proceed',1)) {
            	echo $this->cart->displayButton(JText::_('PROCEED_TO_CHECKOUT'),'checkout',$this->params,$url_checkout,'');
            }
        } 
        ?>
		
		<input type="hidden" name="url" value="<?php echo $this->params->get('url'); ?>"/>
		<input type="hidden" name="ctrl" value="product"/>
		<input type="hidden" name="cart_type" value="<?php echo $this->params->get('cart_type', 'cart'); ?>"/>
		<input type="hidden" name="task" value="updatecart"/>
	</form>
	<?php
    }
?>
</div>
<?php
}
?>

<?php
if (JRequest::getWord('tmpl', '') == 'component') {
    if (!headers_sent()) {
        header('Content-Type: text/css; charset=utf-8');
    }
    exit;
}

// EOF