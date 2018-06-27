/**
 * Created by valmir on 27/06/2018.
 */
sessionStorage.clear();

jQuery(document).ready(function ( $ ) {

    //Appends a switch-button
    if(jQuery("#qrcode").length > 0 && CryptoWoo !== undefined ) {
        var functionName = 'change_addr_' + CryptoWoo.currency;
        if(typeof window[functionName] === "function"){
            jQuery("#check").parent().append('<button id="change_addr_format" onclick="switchPaymentAddress()">Change to cash-address</button>');
        }
    }

});

//Called dynamically
function change_addr_BCH() {
    //Remove qrcode
    jQuery('#qrcode').empty();

    //TODO: Add REGEX to extract/edit only the address part of addressData?
    //TODO: Use newly added "currency" data added in CryptoWoo JSON and make address format based on that
    var currentPaymentAddress = CryptoWoo.payment_address;
    var addr_format_button = jQuery("#change_addr_format");
    if(sessionStorage.qr_payment_address){
        currentPaymentAddress = sessionStorage.qr_payment_address;
    }

    var isLegacy = bchaddr.isLegacyAddress(currentPaymentAddress);
    var isCashAddress = bchaddr.isCashAddress(currentPaymentAddress);

    var addressData = currentPaymentAddress;

    if(isLegacy){
        addressData = bchaddr.toCashAddress(currentPaymentAddress);
        addr_format_button.text("Change to legacy-address");
    }
    else if (isCashAddress){
        addressData = bchaddr.toLegacyAddress(currentPaymentAddress);
        addr_format_button.text("Change to cash-address");
    }
    sessionStorage.qr_payment_address = addressData;

    var addressDataString = addressData;


    if(addressData.indexOf("bitcoincash") == -1){
        addressDataString = 'bitcoincash:' + addressData;
    }
    if(addressData.indexOf("amount") == -1){
        addressDataString += '?amount=' + CryptoWoo.amount;
    }


    //Add qrcode back
    new QRCode(document.getElementById("qrcode"), {
        text: addressDataString,
        width: 250,
        height: 250,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.M
    });


}