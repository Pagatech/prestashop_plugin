<?php
/*
*  @author Pagatech Limited
*  @contributor Tunbosun Ayinla
*  @copyright  Pagatech Limited
*
*/

if (!defined('_PS_VERSION_'))
	exit;

class Paga extends PaymentModule
{

	public function __construct()
	{
		$this->name 		= 'paga';
		$this->tab 			= 'payments_gateways';
		$this->version 		= '1.0';
		$this->author 		= 'Pagatech Limited';
		$this->bootstrap 	= true;

		$paga_epay_code = Configuration::get('PAGA_EPAY_CODE');
		if (!empty( $paga_epay_code ))
			$this->paga_epay_code = $paga_epay_code;
		$paga_mode = Configuration::get('PAGA_MODE');
		if (!empty( $paga_mode ))
			$this->paga_mode = $paga_mode;

		$this->paga_return_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__."modules/{$this->name}/validation.php";

		parent::__construct();

		$this->displayName = 'Paga';
		$this->description = $this->l('Accept payments for your products via Paga accounts, Mastercard, Visa and Verve!');

		$this->confirmUninstall =	$this->l('Are you sure you want to uninstall the Paga Prestashop Payment Gateway Module?');

		if ( !isset( $this->paga_epay_code ) )
			$this->warning = $this->l('Your Merchant Key must be configured before using the Paga Prestashop module.');

		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
	}

	public function install()
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->_errors[] = $this->l('Sorry, this module is not compatible with the version of Prestashop that is installed.');
			return false;
		}

		if (parent::install() == false || !$this->registerHook('displayPayment') || !$this->registerHook('displayPaymentReturn') )
		{
			Logger::addLog('Paga Prestashop Module Installation Failed!', 4);
			return false;
		}

		Configuration::updateValue('PAGA_RETURN_URL', $this->paga_return_url );

		Logger::addLog('Paga Prestashop Module Installation Successful');
		return true;
	}


	public function uninstall()
	{
		if (!Configuration::deleteByName('PAGA_EPAY_CODE')
				|| !Configuration::deleteByName('PAGA_MODE')
				|| !Configuration::deleteByName('PAGA_RETURN_URL')
				|| !$this->unregisterHook('displayPayment')
				|| !$this->unregisterHook('displayPaymentReturn')
				|| !parent::uninstall())
			return false;
		Logger::addLog('Paga Prestashop Module Uninstallation succeed');
		return true;
	}


	private function _displayPagaDetails()
	{
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			$paga_epay_code 	= Tools::getValue('PAGA_EPAY_CODE');
			$mode 				= Tools::getValue('PAGA_MODE');
			$paga_return_url 	= $this->paga_return_url;

			if ($output == null)
			{
				Configuration::updateValue('PAGA_EPAY_CODE', $paga_epay_code);
				Configuration::updateValue('PAGA_MODE', $mode);
				Configuration::updateValue('PAGA_RETURN_URL', $paga_return_url);

				$output .= $this->displayConfirmation($this->l('Paga Settings Updated'));
			}
		}

		return $output.$this->_displayPagaDetails().$this->displayPagaSettingsForm();
	}

	public function displayPagaSettingsForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Paga Merchant Information'),
					'icon' => 'icon-wrench'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Merchant Key'),
						'name' => 'PAGA_EPAY_CODE',
						'desc' => $this->l('Enter your Merchant Key Here'),
						'size' => 30,
						'required' => true
					),
					array (
						'type' => 'radio',
						'label' => $this->l('Test/Live Mode'),
						'name' => 'PAGA_MODE',
						'class' => 't',
						'values' => array (
							array (
								'id' => 'live',
								'value' => 'live',
								'label' => $this->l('Live Phase')
							),
							array (
								'id' => 'test',
								'value' => 'test',
								'label' => $this->l('Test Phase')
							)
						),
						'required' => true
					),
					array(
						'type'		=> 'text',
						'label' 	=> $this->l('Return URL'),
						'name' 		=> 'PAGA_RETURN_URL',
						'desc' 		=> $this->l('This URL should be copied and put in the Payment notification URL field under the Merchant Information section in the E-Pay Set-up area under your Paga Merchant account.'),
						'size' 		=> 40,
						'value'		=> 'tunbosun',
						'disabled'	=> true
					),
				),
				'submit' => array (
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right'
				)
			),
		);

		// Get default Language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

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
		$helper->show_toolbar = false;
		$helper->submit_action = 'submit'.$this->name;


		if (Tools::isSubmit('submit'.$this->name))
		{
			$paga_epay_code 	= Tools::getValue('PAGA_EPAY_CODE');
			$mode 				= Tools::getValue('PAGA_MODE');
			$paga_return_url 	= $this->paga_return_url;
		}
		else
		{
			$paga_epay_code 	= Configuration::get('PAGA_EPAY_CODE');
			$mode 				= Configuration::get('PAGA_MODE');
			$paga_return_url 	= $this->paga_return_ur;
		}


		$helper->fields_value['PAGA_EPAY_CODE'] 	= $paga_epay_code;
		$helper->fields_value['PAGA_MODE'] 			= $mode;
		$helper->fields_value['PAGA_RETURN_URL'] 	= $paga_return_url;

		return $helper->generateForm(array($fields_form));
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function hookDisplayPayment($params)
	{
		if (!$this->active || Configuration::get('PAGA_EPAY_CODE') == '')
			return false;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_bw' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
    }

	public function hookDisplayPaymentReturn($params)
	{

		if (!$this->active)
			return;

		// Get reorder url
		$partial_reorder_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'index.php?controller=order&step=3';
		$reorder_url = $partial_reorder_url.'&id_order='.$params['objOrder']->id.'&submitReorder=Reorder';

		switch ($params['objOrder']->getCurrentState())
		{
			case Configuration::get('PS_OS_PAYMENT'):
				$this->smarty->assign(array(
					'status' 	=> 'ok'
				));
				break;

			case Configuration::get('PS_OS_CANCELED'):
				$this->smarty->assign(array(
					'status' => 'canceled',
					'reorder_url' => $reorder_url
				));
				break;

			case Configuration::get('PS_OS_ERROR'):
			default:
				$this->smarty->assign(array(
					'status' => 'failed',
					'reorder_url' => $reorder_url
				));
				break;
		}
		return $this->display(__FILE__, 'payment_return.tpl');
	}
}
