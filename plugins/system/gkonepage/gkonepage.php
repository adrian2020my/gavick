<?php

/**
* GK OnePage Checkout plugin
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0 $
**/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemGKOnePage extends JPlugin {
	
	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}
	
	function onAfterRoute() {
		// exit when it is back-end view
		if(JFactory::getApplication()->isAdmin()) { return; }
				
		if(JRequest::getCmd('plugin') == 'gkonepage') {
		
		require_once JPATH_SITE.DS.'templates'.DS.JFactory::getApplication()->getTemplate().DS.'html'.DS.'com_virtuemart'.DS.'cart'.DS.'GKCartHelper.php';
		$helper = new GKCartHelper();	
				
		$task = JRequest::getCmd('gktask');
		// save BT and ST address
		if($task == 'saveBTST') {
			$helper->savePayment();
			$helper->saveShipment();
			$helper->saveAddress();	
			
			echo 'default chechout';		
		}
		// save BT, ST and regsiter user
		if($task == 'saveBTSTR') {
			$result = $helper->registerUser();

			echo $result;
		}
		// close to avoid render component area
		JFactory::getApplication()->close();
		}
		
	}
}
?>
