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
{if isset($smarty.get.error)}
  <div class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <i class="material-icons">error_outline</i><p class="alert-text" id="everypay_error"></p>
  </div>
  <script>
    showEverypayError({$smarty.get.error|escape:'htmlall':'UTF-8'});
  </script>
{/if}

<section>
  <p>{l s='Notice: None of your sensitive info are being stored in our website to comply with the PCI-DSS rules' mod='payment_everypay'}</p>
  <p><small>{l s='powered by Everypay Payments' mod='payment_everypay'}</small></p>
</section>
<div id="loader-everypay" style="display: none; position: fixed;height: 100%;width: 100%;background: #f2f2f2;z-index: 100000;top: 0;left: 0;opacity: 0.93;"><center style="width: 100%;position: fixed;clear: both;font-size: 1.3em;top: 40%;margin: 0 auto;"><img style="max-width: 64px; min-width: 64px; max-height: 64px; min-height: 64px;" src="data:image/svg+xml;base64,PHN2ZyBzdHlsZT0ibWF4LXdpZHRoOiA2NHB4OyBtaW4td2lkdGg6IDY0cHg7IG1heC1oZWlnaHQ6IDY0cHg7IG1pbi1oZWlnaHQ6IDY0cHg7IiBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgOTQuMSA5NC4xIj48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6dXJsKCNsaW5lYXItZ3JhZGllbnQpO30uY2xzLTJ7ZmlsbDojMjE0MDlhO30uY2xzLTN7ZmlsbDojMzliNTRhO308L3N0eWxlPjxsaW5lYXJHcmFkaWVudCBpZD0ibGluZWFyLWdyYWRpZW50IiB4MT0iNDcuMDUiIHkxPSItMjYwLjI2IiB4Mj0iNDcuMDUiIHkyPSItMTY2LjE2IiBncmFkaWVudFRyYW5zZm9ybT0ibWF0cml4KDEsIDAsIDAsIC0xLCAwLCAtMTY2LjE2KSIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzM5YjU0YSIvPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzIxNDA5YSIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTk0LjEsNDcuMDVhNDcuMDUsNDcuMDUsMCwxLDEtNDctNDdBNDcsNDcsMCwwLDEsOTQuMSw0Ny4wNVpNNDcsOC40NUEzOC42OSwzOC42OSwwLDEsMCw4NS43Myw0Ny4xNCwzOC42OSwzOC42OSwwLDAsMCw0Nyw4LjQ1WiI+PGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlVHlwZT0ieG1sIiBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iIHR5cGU9InJvdGF0ZSIgZnJvbT0iMCA0Ny4wNSA0Ny4xMCIgdG89IjM2MCA0Ny4wNSA0Ny4xMCIgZHVyPSIxcyIgYWRkaXRpdmU9InN1bSIgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz48L3BhdGg+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNNjYuNjIsMjQuODNjNy43My44NCw2LjM4LDguNTMsNi4zOCw4LjUzTDY2LjE0LDUxLjNIMzBsLTMuNDMsOS4yNUMxOSw1OS41OSwyMSw1Mi4zOCwyMSw1Mi4zOEwzMS4zOCwyNC42N1pNMzYuNzEsMzMuNWwtMy41Nyw5LjI5SDYwLjY1bDIuNTEtNi40NWEyLjUyLDIuNTIsMCwwLDAtMS45My0yLjg0WiIvPjxwYXRoIGNsYXNzPSJjbHMtMyIgZD0iTTI2LjgsNjFTMjQuNzQsNjgsMzIuMDUsNjkuNkg2MC42OHMyLjA2LTcuMTMtNS4yNS04LjUyWiIvPjwvc3ZnPg=="/> <br/><br/>Ολοκλήρωση παραγγελίας. Παρακαλούμε περιμένετε...</center></div>