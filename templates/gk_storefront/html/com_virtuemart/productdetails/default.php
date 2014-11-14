<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz
 * @author RolandD,
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 5151 2011-12-19 17:10:23Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
/* Let's see if we found the product */
if (empty($this->product)) {
	echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

if(JRequest::getInt('print',false)){
?>
<body onload="javascript:print();">
<?php }

// addon for joomla modal Box
JHTML::_('behavior.modal');

$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';

$boxFuncReco = '';
$boxFuncAsk = '';
if(VmConfig::get('usefancy',1)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	if(VmConfig::get('show_emailfriend',0)){
		$boxReco = "jQuery.fancybox({
				href: '" . $MailLink . "',
				type: 'iframe',
				height: '550'
			});";
	}
	if(VmConfig::get('ask_question', 0)){
		$boxAsk = "jQuery.fancybox({
				href: '" . $this->askquestion_url . "',
				type: 'iframe',
				height: '550'
			});";
	}

} else {
	vmJsApi::js( 'facebox' );
	vmJsApi::css( 'facebox' );
	if(VmConfig::get('show_emailfriend',0)){
		$boxReco = "jQuery.facebox({
				iframe: '" . $MailLink . "',
				rev: 'iframe|550|550'
			});";
	}
	if(VmConfig::get('ask_question', 0)){
		$boxAsk = "jQuery.facebox({
				iframe: '" . $this->askquestion_url . "',
				rev: 'iframe|550|550'
			});";
	}
}
if(VmConfig::get('show_emailfriend',0) ){
	$boxFuncReco = "jQuery('a.recommened-to-friend').click( function(){
					".$boxReco."
			return false ;
		});";
}
if(VmConfig::get('ask_question', 0)){
	$boxFuncAsk = "jQuery('a.ask-a-question').click( function(){
					".$boxAsk."
			return false ;
		});";
}

if(!empty($boxFuncAsk) or !empty($boxFuncReco)){
	$document = JFactory::getDocument();
	$document->addScriptDeclaration("
//<![CDATA[
	jQuery(document).ready(function($) {
		".$boxFuncReco."
		".$boxFuncAsk."
	/*	$('.additional-images a').click(function() {
			var himg = this.href ;
			var extension=himg.substring(himg.lastIndexOf('.')+1);
			if (extension =='png' || extension =='jpg' || extension =='gif') {
				$('.main-image img').attr('src',himg );
			}
		});*/
	});
//]]>
");
}
vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
vmJsApi::css('jquery.fancybox-1.3.4');
$document = JFactory::getDocument ();
$imageJS = '
jQuery(document).ready(function() {
	if(jQuery(window).width() > jQuery("body").attr("data-tablet-width")) {
		jQuery("a[rel=vm-additional-images]").fancybox({
			"titlePosition" 	: "inside",
			"transitionIn"	:	"elastic",
			"transitionOut"	:	"elastic"
		});
	} else {
		jQuery("a[rel=vm-additional-images]").click(function(e) {
			e.preventDefault();
		});
	}
	jQuery(".additional-images a.product-image.image-0").removeAttr("rel");
		jQuery(".additional-images img.product-image").click(function() {
			jQuery(".additional-images a.product-image").attr("rel","vm-additional-images" );
			jQuery(this).next().removeAttr("rel");
			var src = jQuery(this).next().attr("href");
			jQuery(".main-image img").attr("src",src);
			jQuery(".main-image img").attr("alt",this.alt );
			jQuery(".main-image a").attr("href",src );
			jQuery(".main-image a").attr("title",this.alt );
			jQuery(".main-image .vm-img-desc").html(this.alt);
		});  
});
';
$document->addScriptDeclaration ($imageJS);

?>
<div class="productdetails-view">
	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = jtext::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
?>
		
		<div class="productDetails">
				<div>
						<?php if (!empty($this->product->images)) {
							$image = $this->product->images[0];
							?>
						<div class="main-image">
						
							<?php
								echo $image->displayMediaFull("",true,"rel='vm-additional-images'");
							?>
		
						</div>
						<?php
							$count_images = count ($this->product->images);
							if ($count_images > 1) {
								?>
						    <div class="additional-images">
			    			<?php
			    			$start_image = VmConfig::get('add_img_main', 1) ? 0 : 1;
			    			for ($i = $start_image; $i < $count_images; $i++) {
			    				$image = $this->product->images[$i];
			    				?>
			    				
			    					<?php
			    					if(VmConfig::get('add_img_main', 1)) {
			    						echo $image->displayMediaThumb('class="product-image" style="cursor: pointer"',false,"");
			    						echo '<a href="'. $image->file_url .'"  class="product-image image-'. $i .'" style="display:none;" title="'. $image->file_meta .'" rel="vm-additional-images"></a>';
			    					} else {
			    						echo $image->displayMediaThumb("",true,"rel='vm-additional-images'");
			    					}
			    					?>
			    		
			    			<?php
			    			}
			    			?>
			    			<div class="clear"></div>
			    		</div>
							<?php
							}
						} ?>
				</div>
				<div>
						<?php // Product Title ?>
							<h1><?php echo $this->product->product_name ?></h1>
							<?php // Product Title END ?>
							<?php // afterDisplayTitle Event
						echo $this->product->event->afterDisplayTitle ?>
							<?php // Product Edit Link
						echo $this->edit_link;
						// Product Edit Link END ?>
						
						<?php if($this->showRating || (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) || (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable'))): ?>
								<div class="product-additional-info">
										<?php if($this->showRating){
										    $maxrating = VmConfig::get('vm_maximum_rating_scale',5);
											$rating = empty($this->rating)? JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED'):JText::_('COM_VIRTUEMART_RATING') . round($this->rating->rating) . '/'. $maxrating;
											echo   $rating;
										} ?>
										<?php // Manufacturer of the Product
										if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) { ?>
										<?php
											$link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $this->product->virtuemart_manufacturer_id . '&tmpl=component', FALSE);
											$text = $this->product->mf_name;
						
											/* Avoid JavaScript on PDF Output */
											if (strtolower(JRequest::getWord('output')) == "pdf"){
												echo JHTML::_('link', $link, $text);
											} else { ?>
										<span class="manufacturer"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') ?></span> <a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $link ?>"><?php echo $text ?></a>
										<?php } ?>
										<?php } ?>
										
										<?php // PDF - Print - Email Icon
							    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) { ?>
										<?php
											    //$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
											    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
											    										
											   echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
										   	   echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
										   	   echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
										   	    ?>
										<?php } // PDF - Print - Email Icon END ?>
								</div>
								<?php endif; ?>
						
						<div class="spacer-buy-area">
								<?php
				if (is_array($this->productDisplayShipments)) {
					foreach ($this->productDisplayShipments as $productDisplayShipment) {
					echo $productDisplayShipment . '<br />';
					}
				}
				if (is_array($this->productDisplayPayments)) {
					foreach ($this->productDisplayPayments as $productDisplayPayment) {
					echo $productDisplayPayment . '<br />';
					}
				}	
				
				// Product Price
				if ($this->show_prices) { ?>
				<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
									
			<?php
			if (!empty($this->product->prices['salesPrice'])) {
				
			}
			echo $this->currency->createPriceDiv ( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices ); 	
			//vmdebug('view productdetails layout default show prices, prices',$this->product);
			if ($this->product->prices['salesPrice']<=0 and VmConfig::get ('askprice', 1) and isset($this->product->images[0]) and !$this->product->images[0]->file_is_downloadable) { ?>
				<a class="ask-a-question bold" href="<?php echo $this->askquestion_url ?>" rel="nofollow" ><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
				<?php
			} else {
				if ($this->showBasePrice) {
					echo $this->currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices);
					if (round($this->product->prices['basePrice'],$this->currency->_priceConfig['basePriceVariant'][1]) != $this->product->prices['basePriceVariant']) {
						echo $this->currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $this->product->prices);
					}
			
				}
				echo $this->currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $this->product->prices);
				if (round($this->product->prices['basePriceWithTax'],$this->currency->_priceConfig['salesPrice'][1]) != $this->product->prices['salesPrice']) {
					echo '<span class="price-crossed" >' . $this->currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $this->product->prices) . "</span>";
				}
				echo $this->currency->createPriceDiv ( 'discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE', $this->product->prices );
				if (round($this->product->prices['salesPriceWithDiscount'],$this->currency->_priceConfig['salesPrice'][1]) != $this->product->prices['salesPrice']) {
					echo $this->currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $this->product->prices);
				}				
				if ($this->product->prices['discountedPriceWithoutTax'] != $this->product->prices['priceWithoutTax']) {
					echo $this->currency->createPriceDiv ('discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices);
				} else {
					echo $this->currency->createPriceDiv ('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices);
				}
				echo $this->currency->createPriceDiv ( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $this->product->prices ); 
				$unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', JText::_('COM_VIRTUEMART_UNIT_SYMBOL_'.$this->product->product_unit));
				echo $this->currency->createPriceDiv ('unitPrice', $unitPriceDescription, $this->product->prices);
				
				if (!empty($this->product->customfieldsSorted['ontop'])) {
					$this->position='ontop';
					echo $this->loadTemplate('customfields');
				} // Product Custom ontop end
					
				
			}
			?>
			
			<?php 
				// Ask a question about this product
				if (VmConfig::get('ask_question', 0) == 1) :
			?>
			<div class="ask-a-question">
			    <a href="<?php echo $this->askquestion_url ?>" class="ask-a-question"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
			</div>
			<?php endif; ?>
			
				</div>
				<?php } ?>
						<?php // Add To Cart Button
							if (!VmConfig::get('use_as_catalog', 0)) { ?>
							<div class="addtocart-area">
									<form method="post" class="product js-recalculate" action="<?php echo JRoute::_('index.php', false); ?>">
											<input name="quantity" type="hidden" value="<?php echo $step; ?>" />
											<?php // Product custom_fields
											if (!empty($this->product->customfieldsCart)) {  ?>
											<div class="product-fields">
													<?php foreach ($this->product->customfieldsCart as $field) { ?>
													<div class="product-field-type-<?php echo $field->field_type ?>">
															<label class="product-fields-title" ><?php echo  vmText::_($field->custom_title) ?></label>
															<?php echo $field->display ?> </div>
													<?php } ?>
											</div>
											<?php }
											 /* Product custom Childs
											  * to display a simple link use $field->virtuemart_product_id as link to child product_id
											  * custom_value is relation value to child
											  */
											
											if (!empty($this->product->customsChilds)) {  
											?>
											<div class="product-fields">
													<?php foreach ($this->product->customsChilds as $field) {  ?>
													<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field->field_type ?>"> <span class="product-fields-title" ><b><?php echo JText::_($field->field->custom_title) ?></b></span> <span class="product-field-desc"><?php echo JText::_($field->field->custom_value) ?></span> <span class="product-field-display"><?php echo $field->display ?></span> </div>
													<br />
													<?php } ?>
											</div>
											<?php } ?>
											
											<?php if ( VmConfig::get ('display_stock', 1) || $this->product->product_box) : ?>
											<dl class="productDetailInfo">
												<?php if ( VmConfig::get ('display_stock', 1)) : ?>
												<dt>
													<?php echo JText::_('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP'); ?>:
												</dt>
												<dd>
													<?php echo $this->product->product_in_stock; ?>
												</dd>
												<?php endif; ?>
												
												<?php if ($this->product->product_box) : ?>
												<dt>
													<?php echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX'); ?>
												</dt>
												<dd>
													<?php echo $this->product->product_box; ?>
												</dd>
												<?php endif; ?>
											</dl>
											<?php endif; ?>
											
											<?php
												if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices['salesPrice'])) {
											?>
													<script type="text/javascript">
															function check(obj) {
													 		// use the modulus operator '%' to see if there is a remainder
															remainder=obj.value % <?php echo $step?>;
															quantity=obj.value;
													 		if (remainder  != 0) {
													 			alert('<?php echo $alert?>!');
													 			obj.value = quantity-remainder;
													 			return false;
													 			}
													 		return true;
													 		}
													</script>   
											
			<div class="addtocart-bar">
			<?php // Display the quantity box 
			$stockhandle = VmConfig::get('stockhandle', 'none');
				if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
				?>
			<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$this->product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
			<?php
			} else {
				$tmpPrice = (float) $this->product->prices['costPrice'];
				if (!( VmConfig::get('askprice', 0) and empty($tmpPrice) ) ) {
					?>
					<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
					<span class="quantity_box_wrap">
						<label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label>
						<span class="quantity-box">
							<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);"
								   value="<?php if (isset($this->product->step_order_level) && (int)$this->product->step_order_level > 0) {
										echo $this->product->step_order_level;
									} else if(!empty($this->product->min_order_level)){
										echo $this->product->min_order_level;
									}else {
										echo '1';
									} ?>"/>
						</span>
						<span class="quantity-controls js-recalculate">
						<input type="button" class="quantity-controls quantity-plus" value="+"/>
						<input type="button" class="quantity-controls quantity-minus" value="-"/>
						</span>
					</span>
					<?php // Display the quantity box END

					// Display the add to cart button ?>
          			<span class="addtocart-button">
          			<?php echo shopFunctionsF::getAddToCartButton ($this->product->orderable);
						// Display the add to cart button END  ?>
         			 </span>
					<input type="hidden" class="pname" value="<?php echo htmlentities($this->product->product_name, ENT_QUOTES, 'utf-8') ?>"/>
					<input type="hidden" name="view" value="cart"/>
					<noscript><input type="hidden" name="task" value="add"/></noscript>
					<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>"/>
				<?php
				}
				?>
			<?php
			}
			?>
					
											</div>
											<?php }
													 // Display the add to cart button END  ?>
											<?php // Display the add to cart button END ?>
											<input type="hidden" name="option" value="com_virtuemart" />
											<?php /** @todo Handle the manufacturer view */ ?>
										</form>
							</div>
							<?php }  // Add To Cart Button END ?>
							
							<?php
							// Availability
							$stockhandle = VmConfig::get('stockhandle', 'none');
							$product_available_date = substr($this->product->product_available_date,0,10);
							$current_date = date("Y-m-d");
							if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
								if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
								?>	<div class="availability">
										<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHTML::_('date', $this->product->product_available_date, JText::_('DATE_FORMAT_LC4')); ?>
									</div>
							    <?php
								} else if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
								?>	<div class="availability">
								    <?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : JText::_(VmConfig::get('rised_availability')); ?>
								</div>
							    <?php
								} else if (!empty($this->product->product_availability)) {
								?>
								<div class="availability">
								<?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : JText::_($this->product->product_availability); ?>
								</div>
								<?php
								}
							}
							else if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
							?>	<div class="availability">
									<?php echo JText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHTML::_('date', $this->product->product_available_date, JText::_('DATE_FORMAT_LC4')); ?>
								</div>
							<?php
							}
							?>
					</div>
				</div>
				
		</div>
		<?php // event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>
	
	
	
	<?php if(!empty($this->product->product_desc) || $this->allowRating || $this->showReview) : ?>
	<ul id="product-tabs">
		<?php if(!empty($this->product->product_desc)) : ?>
		<li data-toggle="product-description"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></li>
		<?php endif; ?>
		
		<?php if($this->allowRating || $this->showReview) : ?>
		<li data-toggle="customer-reviews"><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?></li>
		<?php endif; ?>
	</ul>
	
	<div id="product-tabs-content">
	<?php endif; ?>
	

	<?php if (!empty($this->product->product_desc)) : ?>
	<div class="product-description gk-product-tab">
			<?php echo $this->product->product_desc; ?> 
	</div>
	<?php endif; ?>
		
	<?php // onContentAfterDisplay event
	echo $this->product->event->afterDisplayContent; ?>
		
		
	<?php // Customer Reviews
		if($this->allowRating || $this->showReview) :
			$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
			$ratingsShow = VmConfig::get('vm_num_ratings_show', 3); // TODO add  vm_num_ratings_show in vmConfig
			//$starsPath = JURI::root().VmConfig::get('assets_general_path').'images/stars/';
			$stars = array();
			$showall = JRequest::getBool('showall', false);
			for ($num=0 ; $num <= $maxrating; $num++  ) :
				$title = (JText::_("COM_VIRTUEMART_RATING_TITLE") . $num . '/' . $maxrating) ;
				$stars[] = '<span class="vmicon vm2-stars'.$num.'" title="'.$title.'"></span>';
			endfor; ?>
			
		<div class="customer-reviews gk-product-tab">
			<form method="post" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE); ?>" name="reviewForm" id="reviewform">
		<?php endif; ?>
	
		<?php if($this->showReview) : ?>
		<div class="list-reviews">
			<?php
				$i = 0;
				$review_editable = TRUE;
				$reviews_published = 0;
				if ($this->rating_reviews) {
					foreach ($this->rating_reviews as $review) {						
						if ($i % 2 == 0) {
							$color = 'normal';
						} else {
							$color = 'highlight';
						}
		
						/* Check if user already commented */
						// if ($review->virtuemart_userid == $this->user->id ) {
						if ($review->created_by == $this->user->id && !$review->review_editable) {
							$review_editable = FALSE;
						}
						?>
		
						<?php // Loop through all reviews
						
						if (!empty($this->rating_reviews) /*&& $review->published*/) {
							$reviews_published++;
						?>
							<div class="<?php echo $color ?>"><h3><?php echo $review->customer ?></h3>
								<span class="date"><?php echo JHTML::date ($review->created_on, JText::_ ('DATE_FORMAT_LC')); ?></span>
								<span class="vote"><?php echo $stars[(int)$review->review_rating] ?></span>
								<p><?php echo $review->comment; ?></p>
								
							</div>
							<?php
						}
						$i++;
						if ($i == $ratingsShow && !$showall) {
							/* Show all reviews ? */
							if ($reviews_published >= $ratingsShow) {
								$attribute = array('class'=> 'details', 'title'=> JText::_ ('COM_VIRTUEMART_MORE_REVIEWS'));
								echo JHTML::link ($this->more_reviews, JText::_ ('COM_VIRTUEMART_MORE_REVIEWS'), $attribute);
							}
							break;
						}
					}
		
				} else {
					// "There are no reviews for this product"
					?>
					<span class="step"><?php echo JText::_ ('COM_VIRTUEMART_NO_REVIEWS') ?></span>
					<?php
				}  ?>			
				
		</div>
		<?php endif; ?>
		
		<?php // Writing A Review
			if($this->allowReview ) : ?>
				<div class="write-reviews">
				<?php // Show Review Length While Your Are Writing
				$reviewJavascript = "
				function check_reviewform() {
					var form = document.getElementById('reviewform');
					var ausgewaehlt = false;
	
					for (var i=0; i<form.vote.length; i++) {
						if (form.vote[i].checked) {
							ausgewaehlt = true;
						}
					}
					if (!ausgewaehlt)  {
						alert('".JText::_('COM_VIRTUEMART_REVIEW_ERR_RATE',false)."');
						return false;
					}
					else if (form.comment.value.length < ". VmConfig::get('reviews_minimum_comment_length', 100).") {
						alert('". addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT1_JS', VmConfig::get('reviews_minimum_comment_length', 100)) )."');
						return false;
					}
					else if (form.comment.value.length > ". VmConfig::get('reviews_maximum_comment_length', 2000).") {
						alert('". addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT2_JS', VmConfig::get('reviews_maximum_comment_length', 2000)) )."');
						return false;
					}
					else {
						return true;
					}
				}
	
				function refresh_counter() {
					var form = document.getElementById('reviewform');
					form.counter.value= form.comment.value.length;
				}";
	
				$document->addScriptDeclaration($reviewJavascript);
	
				if($this->showRating) :
					if($this->allowRating && $review_editable) : ?>
						<h4><?php echo JText::_('COM_VIRTUEMART_WRITE_REVIEW')  ?><span><?php echo JText::_('COM_VIRTUEMART_WRITE_FIRST_REVIEW') ?></span></h4>
						<span class="step"><?php echo JText::_('COM_VIRTUEMART_RATING_FIRST_RATE') ?></span>
						<ul class="rating">
						<?php // Print The Rating Stars + Checkboxes
							for ($num=0 ; $num<=$maxrating;  $num++ ) : ?>
							<li id="<?php echo $num ?>_stars">
								<label for="vote<?php echo $num ?>"><?php echo $stars[ $num ]; ?></label>
								<?php $selected = ($num == 5) ? ' checked="checked"' : ''; ?>
								<input<?php echo $selected ?> id="vote<?php echo $num ?>" type="radio" value="<?php echo $num ?>" name="vote">
							</li>
						<?php endfor; ?>
						</ul>
						<?php
					endif;
				endif;
				
				if($review_editable ) : ?>
					<span class="step"><?php echo JText::sprintf('COM_VIRTUEMART_REVIEW_COMMENT', VmConfig::get('reviews_minimum_comment_length', 100), VmConfig::get('reviews_maximum_comment_length', 2000)); ?></span> <br />
					<textarea class="virtuemart" title="<?php echo JText::_('COM_VIRTUEMART_WRITE_REVIEW') ?>" class="inputbox" id="comment" onblur="refresh_counter();" onfocus="refresh_counter();" onkeyup="refresh_counter();" name="comment" rows="5" cols="60">
					<?php if(!empty($this->review->comment)) echo $this->review->comment; ?>
					</textarea>
					<br />
					<span><?php echo JText::_('COM_VIRTUEMART_REVIEW_COUNT') ?>
					<input type="text" value="0" size="4" class="vm-default" name="counter" maxlength="4" readonly />
					</span> <br />
					<br />
					<input class="highlight-button" type="submit" onclick="return( check_reviewform());" name="submit_review" title="<?php echo JText::_('COM_VIRTUEMART_REVIEW_SUBMIT')  ?>" value="<?php echo JText::_('COM_VIRTUEMART_REVIEW_SUBMIT')  ?>" />
				</div>
				<?php
				else :
					echo '<strong>'.JText::_('COM_VIRTUEMART_DEAR').$this->user->name.',</strong><br />' ;
					echo JText::_('COM_VIRTUEMART_REVIEW_ALREADYDONE');
					echo '</div>';
				endif;
			endif;
		
		if($this->allowRating || $this->showReview) :
		?>
					<input type="hidden" name="virtuemart_product_id" value="<?php echo $this->product->virtuemart_product_id; ?>" />
					<input type="hidden" name="option" value="com_virtuemart" />
					<input type="hidden" name="virtuemart_category_id" value="<?php echo JRequest::getInt('virtuemart_category_id'); ?>" />
					<input type="hidden" name="virtuemart_rating_review_id" value="0" />
					<input type="hidden" name="task" value="review" />
			</form>
		<?php
		else :
			echo JText::_('COM_VIRTUEMART_REVIEW_LOGIN'); // Login to write a review!
		endif; ?>
	<?php if($this->allowRating || $this->showReview) : ?>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($this->product->product_desc) || $this->allowRating || $this->showReview) : ?>
	</div><!-- #product-tabs-content -->
	<?php endif; ?>
	
		
	
		<?php
		    if (!empty($this->product->customfieldsRelatedProducts)) {
			echo $this->loadTemplate('relatedproducts');
		    } // Product customfieldsRelatedProducts END
		
		    if (!empty($this->product->customfieldsRelatedCategories)) {
			echo $this->loadTemplate('relatedcategories');
		    } // Product customfieldsRelatedCategories END
		    // Show child categories
		    if (VmConfig::get('showCategory', 1)) {
			echo $this->loadTemplate('showcategory');
		    }
		    if (!empty($this->product->customfieldsSorted['onbot'])) {
		    	$this->position='onbot';
		    	echo $this->loadTemplate('customfields');
		    } // Product Custom ontop end
		    ?>
		    
	<?php
	// Show child categories
	if ( VmConfig::get('showCategory',1) ) {
		if ($this->category->haschildren) {
			$iCol = 1;
			$iCategory = 1;
			$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );
			$category_cellwidth = ' width'.floor ( 100 / $categories_per_row );
			$verticalseparator = " vertical-separator"; ?>
		<div class="category-view">
				<?php // Start the Output
			if(!empty($this->category->children)){
			foreach ( $this->category->children as $category ) {

			// Show the horizontal seperator
			if ($iCol == 1 && $iCategory > $categories_per_row) { ?>
				<div class="horizontal-separator"></div>
				<?php }

			// this is an indicator wether a row needs to be opened or not
			if ($iCol == 1) { ?>
				<div class="row">
						<?php }

			// Show the vertical seperator
			if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

			// Category Link
			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);

				// Show Category ?>
						<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
								<div class="spacer">
										<h3 class="catProductTitle"> <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>"> <?php echo $category->category_name ?> <br />
												<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb("",false);
							//} ?>
												</a> </h3>
								</div>
						</div>
						<?php
			$iCategory ++;

			// Do we need to close the current row now?
			if ($iCol == $categories_per_row) { ?>
						<div class="clear"></div>
				</div>
				<?php
			$iCol = 1;
			} else {
				$iCol ++;
			}
		}
		}
		// Do we need a final closing row tag?
		if ($iCol != 1) { ?>
				<div class="clear"></div>
		</div>
		<?php } ?>
</div>

<?php }
} ?>
</div>
