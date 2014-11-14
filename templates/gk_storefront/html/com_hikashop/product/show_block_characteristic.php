<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

if (!empty ($this->element->characteristics)) {
?><div id="hikashop_product_characteristics" class="hikashop_product_characteristics"><?php
	if($this->params->get('characteristic_display')=='list'){
		if(!empty($this->element->main->characteristics)){
			$display=array('images'=>false,'variant_name'=>false,'product_description'=>false,'prices'=>false);
			$main_images = '';
			if(!empty($this->element->main->images)){
				foreach($this->element->main->images as $image){
					$main_images.='|'.$image->file_path;
				}
			}
			$main_prices = '';
			if(!empty($this->element->main->prices)){
				foreach($this->element->main->prices as $price){
					$main_prices.='|'.$price->price_value.'_'.$price->price_currency_id;
				}
			}
			foreach ($this->element->variants as $variant) {
				foreach($display as $k => $v){
					if(isset($variant->$k) && !is_array($variant->$k)){
						if (!empty($variant->$k)){
							$display[$k] = true;
						}
					}
				}
				$variant_images = '';
				if(!empty($this->element->main->images)){
					if(!empty($variant->images)){
						foreach($variant->images as $image){
							$variant_images.='|'.$image->file_path;
						}
					}
				}
				if($variant_images!=$main_images) $display['images'] = true;
				$variant_prices = '';
				if(!empty($this->element->main->prices)){
					foreach($variant->prices as $price){
						$variant_prices.='|'.$price->price_value.'_'.$price->price_currency_id;
					}
				}
				if($variant_prices!=$main_prices) $display['prices'] = true;
			}
			$columns=0;
			 ?>
			<div class="hikashop_variants_table">
				<?php
					$productClass = hikashop_get('class.product');
					$productClass->generateVariantData($this->element);

					foreach ($this->element->variants as $variant) {
						if(isset($variant->map)) continue; //do not display variants dynamically generated because not in the database
						if(!$this->config->get('show_out_of_stock',1)){
							if($variant->product_quantity==0) continue;
						}
						if(!$variant->product_published) continue;
						$this->row = & $variant; ?>
						<div class="hikashop_variant_row">
							<?php
							if($display['variant_name']){ ?>
								<div class="hikashop_product_name_row" data-label="<?php echo JText::_( 'PRODUCT' ); ?>">
									<?php echo $variant->variant_name; ?>
								</div>
							<?php }
							foreach($this->element->main->characteristics as $characteristic){ ?>
								<div class="hikashop_product_characteristic_row" data-label="<?php echo $characteristic->characteristic_value; ?>">
									<?php
										if(!empty($characteristic->values)){
											foreach($characteristic->values as $k => $value){
												foreach($variant->characteristics as $variantCharacteristic){
													if($variantCharacteristic->characteristic_id==$value->characteristic_id){
														echo $variantCharacteristic->characteristic_value;
														break 2;
													}
												}
											}
										} ?>
								</div>
							<?php }
							if($this->params->get('show_price') && $display['prices']){ ?>
								<div class="hikashop_product_price_row" data-label="<?php echo JText::_( 'PRICE' ); ?>">
									<?php
									$this->params->set('from_module',1);
									$this->setLayout('listing_price');
									echo $this->loadTemplate();
									$this->params->set('from_module',0);
									?>
								</div>
							<?php } ?>
						</div>
					<?php }
				?>
			</div>
			<?php
		}
	}else{
		echo $this->characteristic->displayFE($this->element, $this->params);
	}
?></div><?php
}
