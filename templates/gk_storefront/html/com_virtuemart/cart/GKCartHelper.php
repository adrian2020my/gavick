<?php

defined ('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'cart.php';

class GKCartHelper {
	
	// set base cart values 	
	function __construct() {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$this->cart = VirtueMartCart::getCart(false);
		JFactory::getLanguage()->load('com_virtuemart');
	}
	
	function setBaseData() {
		//
		$this->cart->prepareAddressDataInCart("BT",false);
		$this->cart->prepareAddressDataInCart("ST",false);
		//
		$this->BTaddress=$this->cart->BTaddress;
		$this->STaddress=$this->cart->STaddress;
	}
	
	// save user BTaddress
	function saveAddress() {
		$data = JRequest::get('post');
		
		if(JRequest::getInt('STsameAsBT')=='1') {
			$this->cart->STsameAsBT=1;
			$this->cart->ST=0;
			$this->cart->setCartIntoSession();
		} else {
			$this->cart->STsameAsBT=0;
			if(!strlen($data['shipto_address_type_name'])) {
				$data['shipto_address_type_name']='ST';
			}
		}
		
		if($data['tosAccepted']) {
			$data['agreed']=1;
		} else {
			$data['agreed']=0;
		}
		if($this->cart->STsameAsBT==1) {
			$this->cart->saveAddressInCart($data,'BT');
		} else {
			$this->cart->saveAddressInCart($data,'ST');
			$this->cart->saveAddressInCart($data,'BT');
		}
		
		return $data;
	
	}
	
	function savePayment() {
			
		$data = JRequest::get('post');
	
		$this->cart->setPaymentMethod($data['virtuemart_paymentmethod_id']);
		$this->cart->setCartIntoSession();
		return true;
	}
		
	function saveShipment() {
		
		$data = JRequest::get('post');
	
		$this->cart->setShipment($data['virtuemart_shipmentmethod_id']);
		$this->cart->setCartIntoSession();
		return true;
	}
	
	function registerUser() {
		
		$data = JRequest::get('post');
		$user=VmModel::getModel('user');
		$user->_id = '';
		$output = $user->store($data);
		
		return $output;
	}
}	



// EOF
