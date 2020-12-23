
function addIDtoEverypayLogo() {
    let everypayLogo = document.querySelector('.payment-option label img[src="/modules/payment_everypay/everypay_logo.png"]');
    if (!everypayLogo)
         everypayLogo = document.querySelector('#payment-option-3-container label img');
    try {
        everypayLogo.setAttribute('id', 'everypay_logo');
     } catch (e) {console.log(e)}
}


(function enableEverypayBtnOnAcceptTermsAndConditions() {
    setInterval(function(){
        var acceptTermsAndConditionsButton = document.getElementById("conditions_to_approve[terms-and-conditions]");
        var everypayButton = document.getElementById('everypay_btn');

        if (!acceptTermsAndConditionsButton || !everypayButton) {
            return;
        }

        if (acceptTermsAndConditionsButton.checked){
            everypayButton.removeAttribute("disabled");
            everypayButton.setAttribute('class', 'btn-primary');
        } else {
            everypayButton.setAttribute("disabled", "disabled");
        }
    }, 1000);

})();


var calculate_installments = function (max_installments) {
    var installments = [];

    if (typeof max_installments != 'number' || max_installments > 36)
        return installments;

    var y = 2;
    for (let i = 2; i <= max_installments; i += y) {
        if (i >= 12)
            y = 12;

        installments.push(i);
    }
    return installments;
}

var showEverypayError = function (errorCode) {

    var errorText = '';

    var errorTexts = {
        "errorMerchant": "Παρουσιάστηκε κάποιο σφάλμα. Παρακαλούμε επικοινωνήστε με τον έμπορο!",
        "errorDetails": "Τα στοιχεία της κάρτας είναι λανθασμένα!",
        "errorAuth": "Η κάρτα σας δεν έγινε δεκτή. Παρακαλούμε δοκιμάστε άλλη κάρτα!",
        "errorDefault": "Υπήρξε κάποιο πρόβλημα καθώς επεξεργαζόμασταν την κάρτα σας. Δοκιμάστε ξανά η δοκιμάστε με άλλη κάρτα!",
        "errorCard": "Υπήρξε κάποιο πρόβλημα καθώς επεξεργαζόμασταν την κάρτα σας. Δοκιμάστε με άλλη κάρτα!"
    };

    var everypayErrorElement = document.getElementById('everypay_error');

    if (errorCode < 20000)
        errorText = errorTexts.errorMerchant;
    else if (errorCode >= 20000 && code <= 20003)
        errorText = errorTexts.errorDetails;
    else if (errorCode === 20012)
        errorText = errorTexts.errorDefault;
    else if (errorCode > 20003 && code < 30000 && code !== 20012)
        errorText = errorTexts.errorCard;
    else if (errorCode > 30000 && code < 40000)
        errorText = errorTexts.errorDefault;
    else if (errorCode === 40004)
        errorText = errorTexts.errorAuth;

    if (everypayErrorElement)
        everypayErrorElement.innerHTML = errorText;
}
