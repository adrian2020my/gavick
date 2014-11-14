/**
 *
 * Paybox payment plugin
 *
 * @author Valérie Isaksen
 * @version $Id$
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2014 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

jQuery().ready(function ($) {

    /************/
    /* handlers */
    /************/
    handleDebitType = function () {
        var debit_type = $('#paramsdebit_type').val();

        $('.authorization_only, .authorization_capture').parents('tr').hide();

        if (debit_type == 'authorization_only') {
            $('.authorization_only').parents('tr').show();
        } else if (debit_type == 'authorization_capture') {
            $('.authorization_capture').parents('tr').show();
        }
    }
    handle3Dsecure = function () {
        var activate_3dsecure = $('#paramsactivate_3dsecure').val();

        $('.activate_3dsecure').parents('tr').hide();

        if (activate_3dsecure == 'selective') {
            $('.activate_3dsecure').parents('tr').show();
        } else if (activate_3dsecure == 'active') {
            $('.activate_3dsecure.activate_3dsecure_warning').parents('tr').show();
        }
    }
    handleIntegration = function () {
        var integration = $('#paramsintegration').val();

        $('.integration ').parents('tr').hide();

        if (integration == 'recurring') {
            $('.recurring').parents('tr').show();
        } else if(integration == 'subscribe') {
            $('.subscribe').parents('tr').show();
        }
    }
    handleShopMode = function () {
        var shop_mode = $('#paramsshop_mode').val();

        $('.shop_mode ').parents('tr').hide();

        if (shop_mode == 'test') {
            $('.shop_mode').parents('tr').show();
        }
    }
    /**********/
    /* Events */
    /**********/
    $('#paramsdebit_type').change(function () {
        handleDebitType();

    });
    $('#paramsactivate_3dsecure').change(function () {
        handle3Dsecure();

    });
    $('#paramsactivate_recurring').change(function () {
        handlepPaymentplan();

    });
    $('#paramsshop_mode').change(function () {
        handleShopMode();

    });
    $('#paramsintegration').change(function () {
        handleIntegration();

    });
    /*****************/
    /* Initial calls */
    /*****************/
    handleShopMode();
    handleDebitType();
    handle3Dsecure();
    handleIntegration();
});
