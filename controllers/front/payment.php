<?php

class PagaPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		parent::initContent();

		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

		$url_order_confirmation = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'index.php?controller=order-confirmation';
		$return_url = $url_order_confirmation.'&id_cart='.(int)$cart->id.'&id_module='.(int)$this->id.'&key='.$customer->secure_key;

		$return_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/validation.php';

		$paga_mode = $this->module->paga_mode;
		if( 'test' == $paga_mode){
			$test = 'true';
		}
		else{
			$test = 'false';
		}

		$this->context->smarty->assign(array(
			'nbProducts'		=> $cart->nbProducts(),
			'total' 			=> $cart->getOrderTotal(true, Cart::BOTH),
			'invoice'			=> $cart->id,
			'return_url' 		=> $return_url,
			'paga_mode'			=> $test,
			'paga_js_url'		=> 'https://www.mypaga.com/paga-web/epay/ePay-button.paga?k='.$this->module->paga_epay_code.',&e=false',
			'this_path' 		=> $this->module->getPathUri(),
			'this_path_bw' 		=> $this->module->getPathUri(),
			'this_path_ssl' 	=> Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('payment_execution.tpl');
	}
}
