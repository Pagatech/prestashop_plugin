<?php


class PagaValidationModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$order_status = Tools::getValue('status');

		if( isset($order_status ) )
		{
			$msgs = array();

			$paga = new Paga();

			$callback_amount = (int)Tools::getValue('total');

			/* Prepare cart object */
			$cart = new Cart((int)Tools::getValue('invoice'));

			$customer = new Customer((int)$cart->id_customer);

			$payment_txn_id = Tools::getValue('transaction_id');

			if (version_compare(_PS_VERSION_, '1.5', '>='))
				Context::getContext()->cart = $cart;

			/* Validate amount only if transaction is successful */
			if ($order_status == 'SUCCESS')
			{
				$order_amount = (int)$cart->getOrderTotal();
				if ($order_amount != $callback_amount)
					$msgs[] = 'Possible hack attempt: Amount paid is less than the total order amount.!<br />Amount Paid was: &#8358; '. $callback_amount .' while the total order amount is: &#8358; '. $order_amount .'<br />Paga Transaction ID: '. $payment_txn_id;
			}


			/* Change status */
			switch ($order_status)
			{
				case 'SUCCESS':
					$msgs[] = 'Transaction complete. <br />Paga Transaction ID: '. $payment_txn_id;
					$status = Configuration::get('PS_OS_PAYMENT');
					break;

				case 'ERROR_TIMEOUT':
					$msgs[] = 'Payment Transaction Timeout. Try again later.';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_INSUFFICIENT_BALANCE':
					$msgs[] = 'Insufficient balance in your account.';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_INVALID_CUSTOMER_ACCOUNT':
					$msgs[] = 'Invalid Customer Account';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_CANCELLED':
					$msgs[] = 'Transaction was cancelled';
					$status = Configuration::get('PS_OS_CANCELED');
					break;

				case 'ERROR_BELOW_MINIMUM':
					$msgs[] = 'The order amount is below the minimum allowed. <br />Contact the merchant';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_ABOVE_MAXINUM':
					$msgs[] = 'The order amount is above the maximum allowed. <br />Contact the merchant';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_AUTHENTICATION':
					$msgs[] = 'Invalid Login Details';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				case 'ERROR_UNKNOWN':
					$msgs[] = 'Transaction Failed. Kindly Try again.';
					$status = Configuration::get('PS_OS_ERROR');
					break;

				default:
					$msgs[] = 'Transaction failed';
					$status = Configuration::get('PS_OS_ERROR');
					break;
			}

			/* Get message */
			$message = '';
			foreach ($msgs as $msg)
				$message .= $msg.' ';

			$message = nl2br(strip_tags($message));

			$currency 	= $context->currency;
			$total 		= (float)($cart->getOrderTotal(true, Cart::BOTH));

			/* Validate order */
			$paga->validateOrder(
				$cart->id,
				$status,
				$total,
				$paga->displayName,
				$message,
				array('transaction_id' => $payment_txn_id),
				null,
				false,
				$customer->secure_key
			);

			$order = new Order($paga->currentOrder);

			Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$paga->id.'&id_order='.$paga->currentOrder.'&key='.$customer->secure_key.'&txn_id='.$payment_txn_id);
		}

		Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$paga->id.'&key='.$customer->secure_key);

	}
}
