<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
require_once (_PS_MODULE_DIR_ . 'payment_everypay/everypay-php-master/autoload.php');

use Everypay\Everypay;
use Everypay\Payment;

class Payment_EverypayValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'payment_everypay') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

    		$ctn = $_REQUEST['everypayToken'];

    		if(substr($ctn, 0, 4) !== "ctn_"){
    			die($this->module->l('Unknown payment response. Please contact the website administrator.', 'validation'));
    		}

    		// is cURL installed yet?
    		if (!function_exists('curl_init')){
    			die($this->module->l('PHP cURL module is not installed. Please contact the website administrator.', 'validation'));
    		}

            $customer = new Customer($cart->id_customer);
            if (!Validate::isLoadedObject($customer))
                Tools::redirect('index.php?controller=order&step=1');

            $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

    		if(Configuration::get('EVERYPAY_SANDBOX_MODE'))
    			Everypay::$isTest = true;

    		Everypay::setApiKey(Configuration::get('EVERYPAY_SECRET_KEY'));

    		// exclude notices from the following exception catch
    		error_reporting(E_ERROR | E_WARNING | E_PARSE);

    		try {
    			$payment = Payment::create(array(
    			  "amount" => $total*100,
    			  "currency" => "eur",
    			  "token" => $ctn,
    			  "description" => Configuration::get('PS_SHOP_NAME').' - Order #'.$cart->id_address_invoice,
            "max_installments" => $this->module->_calcInstallments($total)
    			));
          $this->module->validateOrder($cart->id, 2, $total, $this->module->displayName, NULL, array(), $cart->id_currency, false, $customer->secure_key);
          Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    		} catch (Exception $e) {
    			$this->context->smarty->assign([
    			    'error' => $e->getMessage()
    			]);
          $this->setTemplate('module:payment_everypay/views/templates/front/payment_error.tpl');
    			$this->module->validateOrder($cart->id, 8, $total, $this->module->displayName, $e->getMessage(), array(), $cart->id_currency, false, $customer->secure_key);
    		}
    }
}
