{if $status == 'ok'}
	<div class="box">
		<p class="cheque-indent">
			<strong class="dark">{l s='Your order on %s is complete. Payment was received' sprintf=$shop_name mod='paga'}</strong>
		</p>

		<p>
			<strong class="dark">{l s='Payment Transaction ID: %s' sprintf={$smarty.get.txn_id} mod='paga'}</strong>
		</p>
		{l s='Your order is currently being processed.' mod='paga'}<br />
		{l s='You will receive an e-mail when your order has shipped.' mod='paga'}

		<br />{l s='If you have questions, comments or concerns, please contact our' mod='paga'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team. ' mod='paga'}</a>.
	</div>
{else}
	<p class="alert alert-warning">
		{l s='Thank you for shopping with us.' mod='paga'}<br />
		{l s='However, the transaction wasn\'t successful, payment wasn\'t received' mod='paga'} <br />
		{l s='If you think this is an error, feel free to contact our' mod='paga'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team. ' mod='paga'}</a>.
	</p>
{/if}

