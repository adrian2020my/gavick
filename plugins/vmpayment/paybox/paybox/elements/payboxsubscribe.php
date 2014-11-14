<?php
defined("_JEXEC") or die("Direct Access to " . basename(__FILE__) . "is not allowed.");

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  _ Elements
 * @author Valérie Isaksen
 * @link http://www.alatak.net
 * @copyright Copyright (c) 2004 - May 29 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
class JElementpayboxsubscribe extends JElement {

    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    var $_name = "payboxsubscribe";

    function fetchElement($name, $value, &$node, $control_name) {

       $query="
          SELECT virtuemart_custom_id, custom_title
          FROM #__virtuemart_customs
          WHERE (field_type = 'P')
          AND (custom_title LIKE '%".$this->_name."%')

";

	    $db = JFactory::getDBO();
	    $db->setQuery($query);
	    $customFieldList = $db->loadObjectList();

	    $attribs = ' ';
	    $attribs .= ($node->attributes('class') ? ' class="' . $node->attributes('class') . '"' : '');
        return JHTML::_('select.genericlist', $customFieldList, $control_name . '[' . $name . '][]', $attribs, 'virtuemart_custom_id', 'custom_title', $value, $control_name . $name);
    }

}