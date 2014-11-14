<?php
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */
 
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
$gkonepage = JPluginHelper::isEnabled('system', 'gkonepage');
?>


<?php if($gkonepage) : ?>
<?php
// Load helper class
require_once dirname(__FILE__).DS.'GKCartHelper.php';
$this->helper=new GKCartHelper();
$this->helper->setBaseData();

if(VmConfig::get('usefancy',1)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		var con = $('div#full-tos').html();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.fancybox ({ div: '#full-tos', content: con });
		});
	});
	
	document.id('BTaddress').getElements('input').each(function(el) {
		var cval=validator.validate(el);
		valid=valid && cval;
	});
	if(!valid) {
		window.location.hash ='cart_top';
		return;
	}
//]]>
";
} else {
	vmJsApi::js ('facebox');
	vmJsApi::css ('facebox');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
";
}

JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument();
$document->addScriptDeclaration ($box);



$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
	if ( $('#STasBT').is(':checked') ) {
				$('#STaddress').hide();
				$('#STasBT').val('1') ;
				$('#STsameAsBT').val('1');
			} else {
				$('#STaddress').show();
				$('#STasBT').val('0') ;
				$('#STsameAsBT').val('0');
			}
		$('#STasBT').click(function(event) {
			if($(this).is(':checked')){
				$('#STasBT').val('1') ;
				$('#STsameAsBT').val('1');
				$('#STaddress').hide();
			} else {
				$('#STasBT').val('0') ;
				$('#STsameAsBT').val('0');
				$('#STaddress').show();
			}
		});
		
		var validator=new JFormValidator();
		validator.attachToForm(document.id('BTaddress'));
		
		
		$('button[name=checkout]').click(function(e) {
			
			var valid = true;
			document.id('BTaddress').getElements('input').each(function(el) {
				var cval=validator.validate(el);
				valid=valid && cval;
			});
			if(valid && $('#tosAccepted').is(':checked')) {
				e.preventDefault();
				e.stopPropagation();
				//			
				if($('#register').is(':checked')) {
					// additional ajax query to register user
					jQuery.ajax({
					  type: 'POST',
					  url: 'index.php?plugin=gkonepage&gktask=saveBTSTR',
					  data: jQuery('#checkoutForm').serialize()+'&address_type=BT&'+$('#sToken').val()+'=1',
					}).done(function(msg) {
						//$('#terms-of-service').html(msg);
					});
				}
				var data = $('#checkoutForm').serialize();
				jQuery.ajax({
				  type: 'POST',
				  url: 'index.php?plugin=gkonepage&gktask=saveBTST',
				  data: data
				}).done(function(msg) {
					//$('#checkoutStatus').html(msg);
				    javascript:document.checkoutForm.submit();
				});
			} else {
				if(!$('#tosAccepted').is(':checked')) {
					alert('<?php echo JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS'); ?>');
				} else {
					var msg = '<?php echo addslashes (vmText::_ ('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS')); ?>';
					alert(msg + ' ');
				}
				e.preventDefault();
				e.stopPropagation();
			}
		});
	
		
	});
</script>

<div class="cart-view onepage">
	<div>
		
			<h3><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h3>
		
		<?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') {
		vmdebug ('checkout_task', $this->checkout_task);
		echo '<div class="checkoutStep" id="checkoutStep4">' . JText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
	} ?>
		<div>
			<?php // Continue Shopping Button
			if (!empty($this->continue_link_html)) {
				echo $this->continue_link_html;
			} ?>
		</div>
		
	</div>



	<?php echo shopFunctionsF::getLoginForm ($this->cart, FALSE);
	
	// This displays the form to change the current shopper
	$adminID = JFactory::getSession()->get('vmAdminID');
	if ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser($adminID)->authorise('core.admin', 'com_virtuemart')) && (VmConfig::get ('oncheckout_change_shopper', 0))) { 
		echo $this->loadTemplate ('shopperform');
	}
	
	if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}
	
	?>
	
	<div id="checkout-advertise-box">
		<?php
		if (!empty($this->checkoutAdvertise)) {
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>

	<?php
	if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}
	
	?>
		<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
		
		<?php
		// This displays the pricelist MUST be done with tables, because it is also used for the emails
			echo $this->loadTemplate ('pricelist_onepage');
		
		
		?>
		
		<?php // Leave A Comment Field ?>
		<div class="customer-comment marginbottom15">
			<span class="comment"><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></span><br/>
			<textarea class="customer-comment" name="customer_comment" cols="60" rows="1"><?php echo $this->cart->customer_comment; ?></textarea>
		</div>
		<?php // Leave A Comment Field END ?>



		<?php // Continue and Checkout Button ?>
		<div class="checkout-button-top">

			<?php // Terms Of Service Checkbox
			if (!class_exists ('VirtueMartModelUserfields')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
			}
			$userFieldsModel = VmModel::getModel ('userfields');
			if ($userFieldsModel->getIfRequired ('agreed')) {
					if (!class_exists ('VmHtml')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
					}
					echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"');

					if (VmConfig::get ('oncheckout_show_legal_info', 1)) {
						?>
						<div class="terms-of-service">

							<label for="tosAccepted">
								<a href="<?php JRoute::_ ('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=1', FALSE) ?>" class="terms-of-service" id="terms-of-service" rel="facebox"
							  	 target="_blank">
									<span class="vmicon vm2-termsofservice-icon"></span>
									<?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>
								</a>
							</label>

							<div id="full-tos">
								<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h2>
								<?php echo $this->cart->vendor->vendor_terms_of_service; ?>
							</div>

						</div>
						<?php
					} 
			}
			
			?>
		</div>
		
		<?php  
		// Continue and Checkout Button END ?>
		<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='confirm'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
		<input type='hidden' name='sToken' id="sToken" value="<?php echo JUtility::getToken(); ?>" />
		<?php echo $this->loadTemplate('address'); ?>
		<div id="checkoutStatus"></div>
		<?php echo $this->checkout_link_html; ?>
		
	</form>
</div>


<?php else : ?>
<?php
	// default layout
if(VmConfig::get('usefancy',0)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		var con = $('div#full-tos').html();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.fancybox ({ div: '#full-tos', content: con });
		});
	});

//]]>
";
} else {
	vmJsApi::js ('facebox');
	vmJsApi::css ('facebox');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
";
}

JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument ();
$document->addScriptDeclaration ($box);
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
		$('#STsameAsBTjs').click(function(event) {
			if($(this).is(':checked')){
				$('#STsameAsBT').val('1') ;
				$('#output-shipto-display').hide();
			} else {
				$('#STsameAsBT').val('0') ;
				$('#output-shipto-display').show();
			}
		});
	});

//]]>

");
$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

?>

<div class="cart-view">
	<div>
		<div>
			<h3><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h3>
		</div>
		<?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') {
		vmdebug ('checkout_task', $this->checkout_task);
		echo '<div class="checkoutStep" id="checkoutStep4">' . JText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
	} ?>
		<div>
			<?php // Continue Shopping Button
			if (!empty($this->continue_link_html)) {
				echo $this->continue_link_html;
			} ?>
		</div>
		
	</div>



	<?php echo shopFunctionsF::getLoginForm ($this->cart, FALSE);
	
	// This displays the form to change the current shopper
	$adminID = JFactory::getSession()->get('vmAdminID');
	if ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser($adminID)->authorise('core.admin', 'com_virtuemart')) && (VmConfig::get ('oncheckout_change_shopper', 0))) { 
		echo $this->loadTemplate ('shopperform');
	}
	
	// This displays the pricelist MUST be done with tables, because it is also used for the emails
	echo $this->loadTemplate ('pricelist');

	// added in 2.0.8
	?>
	<div id="checkout-advertise-box">
		<?php
		if (!empty($this->checkoutAdvertise)) {
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>

	<?php
	if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}
	?>
		<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">

		<?php // Leave A Comment Field ?>
		<div class="customer-comment marginbottom15">
			<span class="comment"><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></span><br/>
			<textarea class="customer-comment" name="customer_comment" cols="60" rows="1"><?php echo $this->cart->customer_comment; ?></textarea>
		</div>
		<?php // Leave A Comment Field END ?>



		<?php // Continue and Checkout Button ?>
		<div class="checkout-button-top">

			<?php // Terms Of Service Checkbox
			if (!class_exists ('VirtueMartModelUserfields')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
			}
			$userFieldsModel = VmModel::getModel ('userfields');
			if ($userFieldsModel->getIfRequired ('agreed')) {
					if (!class_exists ('VmHtml')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
					}
					echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"');

					if (VmConfig::get ('oncheckout_show_legal_info', 1)) {
						?>
						<div class="terms-of-service">

							<label for="tosAccepted">
								<a href="<?php JRoute::_ ('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=1', FALSE) ?>" class="terms-of-service" id="terms-of-service" rel="facebox"
							  	 target="_blank">
									<span class="vmicon vm2-termsofservice-icon"></span>
									<?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>
								</a>
							</label>

							<div id="full-tos">
								<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h2>
								<?php echo $this->cart->vendor->vendor_terms_of_service; ?>
							</div>

						</div>
						<?php
					} 
			}
			echo $this->checkout_link_html;
			?>
		</div>
		<?php // Continue and Checkout Button END ?>
		<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>
</div>
<?php endif; ?>