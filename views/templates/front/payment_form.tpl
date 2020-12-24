{*
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
*}
<form class="payment-card-form" method="POST" action="{$action}" id="everypay-form">
	<input type="hidden" name="everypayToken" value="" id="everypayToken">
	<button id="everypay_btn" class="btn btn-primary" disabled="disabled">Πληρωμή με κάρτα</button>

	<script>

		if ("{$locale}" != 'el')
			document.getElementById('everypay_btn').innerHTML = "Pay with Card";

		let payload = {
			pk: "{$pk}",
			amount: {$amount},
			locale: "{$locale}",
			data: {
				billing: {
					addressLine1: "{$billingAddress}",
					postalCode: "{$postalCode}",
					city: "{$city}"
				}
			},
		};

		if ({$installments})
			payload.installments = calculate_installments({$installments});

		let modal = new EverypayModal();
		document.getElementById('everypay_btn').addEventListener('click', function (event) {
			event.preventDefault();

			everypay.payform(payload, (response) => {

				if (response.onLoad)
					modal.open();

				if (response.response == 'success') {
					modal.destroy();
					document.getElementById('everypayToken').value = response.token;
					document.getElementById('everypay-form').submit()
					document.getElementById("loader-everypay").style.display = 'flex';
				}

				});

		});

	</script>
</form>