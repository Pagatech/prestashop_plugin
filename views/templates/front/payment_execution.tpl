{capture name=path}
    {l s='Payment Method: Paga' mod='paga'}
{/capture}

<h1 class="page-heading">
    {l s='Order summary' mod='paga'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="alert alert-warning">
        {l s='Your shopping cart is empty.' mod='paga'}
    </p>
{else}
    <form action="{$link->getModuleLink('paga', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
        <div class="box cheque-box">
            <h3 class="page-subheading">
                {l s='Paga Payment.' mod='paga'}
            </h3>
            <p class="cheque-indent">
                <strong class="dark">
                    {l s='You have chosen to pay by Paga.' mod='paga'} {l s='Here is a short summary of your order:' mod='paga'}
                </strong>
            </p>
            <p>
                - {l s='The total amount of your order is' mod='paga'}
                <span id="amount" class="price">{displayPrice price=$total}</span>
                {if $use_taxes == 1}
                    {l s='(tax incl.)' mod='paga'}
                {/if}
            </p>
            <p>
                - {l s='Please select your payment method and click the "Proceed..." button' mod='paga'}

				<form method="post">
					<input type="hidden" name="description" value="Payment for Order ID: #{$invoice} on {$shop_name}" />
					<input type="hidden" name="subtotal" value="{$total}" />
					<input type="hidden" name="invoice" value="{$invoice}" />
					<input type="hidden" name="return_url" value="{$return_url}" />
					<input type="hidden" name="test" value="{$paga_mode}" />
				</form>

				{literal}
					<style type="text/css">
						#__p_ew_l table{
							width: 100%;
						}
					</style>
				{/literal}

				<!-- begin Paga ePay widget code -->
				{literal}
				<script type="text/javascript" src="{/literal}{$paga_js_url}{literal}"></script>
				{/literal}
				<!-- end Paga ePay widget code -->
			</p>

        </div>

        <p class="cart_navigation clearfix" id="cart_navigation">
        	<a
            class="button-exclusive btn btn-default"
            href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
                <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='paga'}
            </a>
        </p>
    </form>
{/if}







