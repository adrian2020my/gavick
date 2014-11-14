<?php

$this->helper->setBaseData();
?>
<div class="vmBTST">

	<div class="vmrow">
		 <div class="output-billto"><h3><?php echo JText::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'); ?> </h3>
		<?php
		if(JFactory::getUser()->get('id')>0) {
			?>
			</div>
			<?php
		}
		if(JFactory::getUser()->get('id')==0 && VmConfig::get('oncheckout_show_register')) {
			?>
			<div class="register">
			<input class="inputbox" type="checkbox" name="register" id="register" value="1"/>
			<?php echo JText::_('COM_VIRTUEMART_REGISTER'); ?> </div></div>
		<?php
		}
		$userFields=array('agreed','name','username','password','password2');
		echo '<div id="BTaddress">';
		echo '	<div class="adminform user-details gkleft"'.'>' . "\n";
		$counter = 0;
		foreach($this->helper->BTaddress["fields"] as $_field) {
			if($counter == 2) {
				echo '</div><div class="gkright">';
			}
			if(!in_array($_field['name'],$userFields)) {
				continue;
			}
			if($_field['name']=='agreed') {
				continue;
			}  
			
		    echo '				<label class="' . $_field['name'] . ' full-input" for="' . $_field['name'] . '_field">' . "\n";
		    echo '					' . $_field['title'] . ($_field['required'] ? ' *' : '') . "\n";
		    echo '				</label>' . "\n";
		    echo '				' . $_field['formcode'] . "\n";
		    $counter++;
		}
		echo '</div><div class="vmrow">';
		echo '<div class="gkleft">';
		
		$counter = 0;
		foreach($this->helper->BTaddress["fields"] as $_field) {
			if(in_array($_field['name'],$userFields)) {
				continue;
			} 
			if($counter == 10) {
				echo '</div><div class="gkright BTdetails">';
			}
			if($_field['name'] == 'delimiter_userinfo') {
				echo '<h3>'. $_field['title'].'';
			} else if($_field['name'] == 'delimiter_billto') {
				echo ' ('.$_field['title'].')</h3>';
			} else {
		    echo '				<label class="' . $_field['name'] . ' full-input" for="' . $_field['name'] . '_field">' . "\n";
		    echo '					' . $_field['title'] . ($_field['required'] ? ' *' : '') . "\n";
		    echo '				</label>' . "\n";
		   
		    echo '				' . $_field['formcode'] . "\n";
		    }
		    $counter++;
		}
	    echo '	</div>' . "\n";
	    echo '</div>';
		?>
	</div>

	<div class="vmrow">
		  <div class="output-shipto">
		<?php
		if(!empty($this->cart->STaddress['fields'])){
			if(!class_exists('VmHtml'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
				echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT');
				?>
				<input class="inputbox" type="checkbox" name="STsameAsBT" id="STasBT" value="1" checked="true"/>
				<?php
		}
 		?>
		
		</div>
		<?php if(!isset($this->cart->lists['current_id'])) $this->cart->lists['current_id'] = 0; ?>
		<?php
		echo '	<div class="adminform user-details" id="STaddress" '.'>' . "\n";
		echo '<div class="gkleft">';
		$counter = 0;
		foreach($this->helper->STaddress["fields"] as $_field) {
			$counter++;
			if($counter == 8) { 
				echo '</div>';
				echo '<div class="gkright">';
			}	
		    echo '				<label class="' . $_field['name'] . '" for="' . $_field['name'] . '_field">' . "\n";
		    echo '					' . $_field['title'] . ($_field['required'] ? ' *' : '') . "\n";
		    echo '				</label>' . "\n";
		    if($_field['name']=='shipto_zip') {
		    	$_field['formcode']=str_replace('input','input onchange="update_form();"',$_field['formcode']);
		    } else if($_field['name']=='shipto_virtuemart_country_id') {
		    	$_field['formcode']=str_replace('<select','<select onchange="update_form();add_countries();"',$_field['formcode']);
		    	$_field['formcode']=str_replace('class="virtuemart_country_id','class="shipto_virtuemart_country_id',$_field['formcode']);
		    } else if($_field['name']=='shipto_virtuemart_state_id') {
		    	$_field['formcode']=str_replace('id="virtuemart_state_id"','id="shipto_virtuemart_state_id"',$_field['formcode']);
		    	$_field['formcode']=str_replace('<select','<select onchange="update_form();"',$_field['formcode']);
		    }
		    echo '				' . $_field['formcode'] . "\n";
		}
	    echo '</div></div>' . "\n";
		?>

	</div>

</div>
