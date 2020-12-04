
(function addIDtoEverypayLogo() {
    let everypayLogo = document.querySelector('.payment-option label img[src="/modules/payment_everypay/everypay_logo.png"]');
    if (!everypayLogo)
        everypayLogo = document.querySelector('#payment-option-3-container label img');
    everypayLogo.setAttribute('id', 'everypay_logo');
})();



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

var tosChecker = setInterval(function(){
    if(document.getElementById("conditions_to_approve[terms-and-conditions]").checked){
        document.querySelector('#everypay_btn').removeAttribute("disabled");
    } else {
        document.querySelector('#everypay_btn').setAttribute("disabled", "disabled");
    }
}, 1000);
