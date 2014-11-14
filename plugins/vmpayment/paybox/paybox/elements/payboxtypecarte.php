<?php
defined("_JEXEC") or die("Direct Access to " . basename(__FILE__) . "is not allowed.");

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  _ Elements
 * @author Valérie Isaksen
 * @copyright Copyright (c) 2004 - May 29 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
class JElementpayboxtypecarte extends JElement {

    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    var $_name = "payboxtypecarte";

    function fetchElement($name, $value, &$node, $control_name) {

        $paymentMeans = array(
            "CARTE" => array(
                "CB",
                "VISA",
                "EUROCARD_MASTERCARD",
                "ECARD",
                "AMEX",
                "DINERS",
                "JCB",
                "AURORE",
            ),
            "PAYPAL" => array(
                "PAYPAL"
            ),
            "CREDIT" => array(
                "UNEURO",
                "34ONEY",
            ),
            "NETRESERVE" => array(
                "NETCDGP"
            ),
            "PREPAYEE" => array(
                "SVS",
                "KADEOS",
                "PSC",
                "CSHTKT",
                "LASER",
                "EMONEO",
                "IDEAL",
                "ONEYKDO",
                "ILLICADO",
                "WEXPAY",
                "MAXICHEQUE",
            ),
            "CREDIT" => array(
                "SURCOUF",
                "KANGOUROU",
                "FNAC",
                "CYRILLUS",
                "PRINTEMPS",
                "CONFORAMA",
            ),
            "BUYSTER" => array(
                "BUYSTER",
            ),
            "LEETCHI" => array(
                "LEETCHI",
            ),
            "PAYBUTTONS" => array(
                "PAYBUTTONS"
            ),
        );


	$i = 0;
	foreach ($paymentMeans as $key => $fields) {
        if ($key=="CARTE") { // pour l'instant
            foreach ($fields as $field){
                $paymentMeansArray[$i]['value'] = $field;
                $paymentMeansArray[$i++]['text'] =  vmText::_('VMPAYMENT_'.$this->_name.'_TYPECARTE_' . strtoupper($field));
            }
        }

    }

        $class = 'multiple="true" size="10"';
        return JHTML::_('select.genericlist', $paymentMeansArray, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);
    }

}