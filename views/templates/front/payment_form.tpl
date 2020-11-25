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

<form class="payment-card-form" method="POST" action="{$action}" >
	<script type="text/javascript" class="everypay-script"
			src="https://button.everypay.gr/js/button.js"
			data-key="{$pk}"
			data-amount="{$total}"
			data-locale="{$locale}"
			{if $sandbox eq 1}
				data-sandbox="1"
			{/if}
			{if $installments gt 0}
				data-max_installments="{$installments}"
			{/if}
			data-description="{$desc}">
	</script>
	<script>


		var tosChecker = setInterval(function(){
			if(document.getElementById("conditions_to_approve[terms-and-conditions]").checked){
				document.querySelector('.everypay-button').removeAttribute("disabled");
			} else {
				document.querySelector('.everypay-button').setAttribute("disabled", "disabled");
			}
		}, 1000);



		let getEverypayErrorTextWithCode = function (code) {

			let errorText = {
				"errorMerchant": "Παρουσιάστηκε κάποιο σφάλμα. Παρακαλούμε επικοινωνήστε με τον έμπορο!",
				"errorDetails": "Τα στοιχεία της κάρτας είναι λανθασμένα!",
				"errorAuth": "Η κάρτα σας δεν έγινε δεκτή. Παρακαλούμε δοκιμάστε άλλη κάρτα!",
				"errorDefault": "Υπήρξε κάποιο πρόβλημα καθώς επεξεργαζόμασταν την κάρτα σας. Δοκιμάστε ξανά η δοκιμάστε με άλλη κάρτα!",
				"errorCard": "Υπήρξε κάποιο πρόβλημα καθώς επεξεργαζόμασταν την κάρτα σας. Δοκιμάστε με άλλη κάρτα!"
			};

			if (code < 20000) {
				return errorText.errorMerchant;
			}
			if (code >= 20000 && code <= 20003) {
				return errorText.errorDetails;
			}
			if (code === 20012) {
				return errorText.errorDefault;
			}
			if (code > 20003 && code < 30000 && code !== 20012) {
				return errorText.errorCard;
			}

			if (code > 30000 && code < 40000) {
				return errorText.errorDefault;
			}

			if (code === 40004) {
				return errorText.errorAuth;
			}

			return errorText.errorDefault;
		}

		{if isset($smarty.get.error)} {
			let payment_error = getEverypayErrorTextWithCode({$smarty.get.error|escape:'htmlall':'UTF-8'});

			setTimeout(function () {
				alert(payment_error);
			}, 500);
		}
		{/if}

	</script>
</form>