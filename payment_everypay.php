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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Payment_Everypay extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = array();

    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;

    const OPEN = 1;
    const SUCCESS = 2;
    const ERRORNEOUS = 3;
    const CLOSED = 0;
    const ORDER_STATUS_CAPTURE_PENDING = 11;

    public  $configuration;

    private $online;
    private $adminMessages;
    private $defaults;
    private $redirectOnCheck;
    private $pk;
    private $sk;

    public function __construct()
    {
        $this->name = 'payment_everypay';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author = 'Everypay';
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Everypay Payments');
        $this->description = $this->l('Accept credit/debit card payments with Everypay payment gateway');

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }

        $this->redirectOnCheck = true;
        $this->live_mode = 0;
        $this->mode = 'test';
        $this->pk = $this->sk = '';

        $this->defaults = array(
            'EVERYPAY_PUBLIC_KEY'      => '',
            'EVERYPAY_SECRET_KEY'      => '',
            'EVERYPAY_CUSTOMER_MODE'   => false,
            'EVERYPAY_BUTTON_MODE'     => true,
            'EVERYPAY_LIVE_MODE'       => false
        );
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('paymentOptions') || !$this->registerHook('paymentReturn') || !$this->registerHook('displayHeader')) {
            return false;
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        if ($this->context->controller->php_self != 'order')
            return;

        $this->context->controller->registerStylesheet('everypay_modal_css', 'modules/'.$this->name.'/views/css/everypay_modal.css', array('media' => 'all', 'priority' => 0,  'server' => 'local', 'position' => 'head'));

        $this->context->controller->registerStylesheet('everypay_css', 'modules/'.$this->name.'/views/css/everypay_styles.css', array('media' => 'all', 'priority' => 0,  'server' => 'local', 'position' => 'head'));

        $this->context->controller->registerJavascript('everypay_modal_js',  'modules/'.$this->name.'/views/js/everypay_modal.js', array('media' => 'all', 'priority' => 1, 'inline' => false, 'server' => 'local', 'position' => 'head'));
        $this->context->controller->registerJavascript('everypay_js',  'modules/'.$this->name.'/views/js/everypay.js', array('media' => 'all', 'priority' => 1, 'inline' => false, 'server' => 'local', 'position' => 'head'));


        if (Configuration::get('EVERYPAY_SANDBOX_MODE'))
             $this->context->controller->registerJavascript('everypay_iframe', 'https://sandbox-js.everypay.gr/v3', array('media' => 'all', 'priority' => 1, 'inline' => true, 'server' => 'remote', 'position' => 'head'));
        else
            $this->context->controller->registerJavascript('everypay_iframe', 'https://js.everypay.gr/v3', array('media' => 'all', 'priority' => 1, 'inline' => true, 'server' => 'remote', 'position' => 'head'));

    }

	/**
	 * Backend options view
	 * @return string
	*/
	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			$sandbox = strval(Tools::getValue('EVERYPAY_SANDBOX_MODE'));
			$pk = strval(Tools::getValue('EVERYPAY_PUBLIC_KEY'));
			$sk = strval(Tools::getValue('EVERYPAY_SECRET_KEY'));
			$inst = strval(Tools::getValue('EVERYPAY_INSTALLMENTS'));

			if((!$pk || empty($pk) || !Validate::isGenericName($pk)) || (!$sk || empty($sk) || !Validate::isGenericName($sk)))
				$output .= $this->displayError($this->l('You need to input your API keys'));
			else
			{
				Configuration::updateValue('EVERYPAY_SANDBOX_MODE', $sandbox);
				Configuration::updateValue('EVERYPAY_PUBLIC_KEY', $pk);
				Configuration::updateValue('EVERYPAY_SECRET_KEY', $sk);
				Configuration::updateValue('EVERYPAY_INSTALLMENTS', $inst);
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->displayForm();
	}

	/**
	 * Backend options form
	 * return string
	*/
	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
				'icon' => 'icon-cogs'
			),
			'input' => array(
				array(
				  'type'      => 'radio',
				  'label'     => $this->l('Sandbox Mode'),
				  'desc'      => $this->l('If enabled no real transactions will be made. Enable for testing mode.'),
				  'name'      => 'EVERYPAY_SANDBOX_MODE',
				  'required'  => true,
				  'class'     => 't',
				  'is_bool'   => true,
				  'values'    => array(
					array(
					  'id'    => 'sandbox_on',
					  'value' => 1,
					  'label' => $this->l('Enabled')
					),
					array(
					  'id'    => 'sandbox_off',
					  'value' => 0,
					  'label' => $this->l('Disabled')
					)
				  )
				),
				array(
					'type' => 'text',
					'label' => $this->l('Public Key'),
					'name' => 'EVERYPAY_PUBLIC_KEY',
					'size' => 20,
					'required' => true,
					'empty_message' => $this->l('If you haven\'t registered, do so now at everypay.gr to get your API keys')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Secret Key'),
					'name' => 'EVERYPAY_SECRET_KEY',
					'size' => 20,
					'required' => true,
					'empty_message' => $this->l('If you haven\'t registered, do so now at everypay.gr to get your API keys')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Installments'),
					'name' => 'EVERYPAY_INSTALLMENTS',
					'size' => 20,
					'desc' => $this->l('Format: total_min:total_max:max_installments; Eg 45.00:99.99:3;100:299.99:6;')
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-success pull-right'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Load current value
		$helper->fields_value['EVERYPAY_SANDBOX_MODE'] = Configuration::get('EVERYPAY_SANDBOX_MODE');
		$helper->fields_value['EVERYPAY_PUBLIC_KEY'] = Configuration::get('EVERYPAY_PUBLIC_KEY');
		$helper->fields_value['EVERYPAY_SECRET_KEY'] = Configuration::get('EVERYPAY_SECRET_KEY');
		$helper->fields_value['EVERYPAY_INSTALLMENTS'] = Configuration::get('EVERYPAY_INSTALLMENTS');

		return $helper->generateForm($fields_form);
	}



    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $billingAddress = (
            new Address(intval($params['cart']->id_address_delivery))
        )->address1;

        $paymentOpt = new PaymentOption();
        $paymentOpt->setCallToActionText($this->l('Pay with Credit/Debit Card'))
                       ->setForm($this->generateForm($billingAddress))
                       ->setAdditionalInformation($this->context->smarty->fetch('module:payment_everypay/views/templates/front/payment_infos.tpl'))
					   ->setBinary(true)
                    ->setLogo($this->_path.'everypay_logo.png');

        return array($paymentOpt);
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function generateForm($billingAddress)
    {
		$cart = $this->context->cart;
		$total = (float) $cart->getOrderTotal(true, Cart::BOTH);

		$lang = ($this->context->language->iso_code == "el") ? "el" : "en";

        $this->context->smarty->assign([
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
            'pk' => Configuration::get('EVERYPAY_PUBLIC_KEY'),
            'amount' => $total * 100,
            'locale' => $lang,
            'txnType' => 'tds',
            'hidden' => true,
            'installments' => $this->_calcInstallments($total),
            'billingAddress' => $billingAddress
        ]);

        return $this->context->smarty->fetch('module:payment_everypay/views/templates/front/payment_form.tpl');
    }

	public function _calcInstallments($total){
		$inst = Configuration::get('EVERYPAY_INSTALLMENTS');
		if(strlen($inst) < 5){
			return 0;
		}
		$max = 0;

		// replace comma seperated decimals
		$inst = str_replace(",", ".", $inst);

		$options = explode(";", $inst);

		// remove empty option
		$options = array_filter($options);

		foreach($options as $key => $option){
			if(strpos($option, ":")){
				$installment = explode(":", $option);
				if(count($installment) < 3) continue;
				if($total >= floatval($installment[0]) && $total <= floatval($installment[1]) && $max < $installment[2]) $max = $installment[2];
			}
		}

		return $max;
	}
}
