/**
 * Created by valmir on 27/06/2018.
 */
sessionStorage.clear();

jQuery(document).ready(function ( $ ) {

    //Appends switch-button and qr_code
    //TODO-Improvement: Refactor, more methods, DRY, etc.
    if(jQuery("#qrcode").length > 0 && CryptoWoo !== undefined ) {
        var functionName = 'change_addr_' + CryptoWoo.currency;
        if(typeof window[functionName] === "function"){
            jQuery("#check").parent().append('<button class="btn-change-addr-format" id="change_addr_format" onclick="switchPaymentAddress()">Change to legacy-address</button>');
        }
        if(CryptoWoo.currency == 'BCH' && bchaddr.isLegacyAddress(CryptoWoo.payment_address)){
            jQuery('#qrcode').empty();
            var currentAddress = jQuery("#payment-address").text();
            var cashAddressWithoutLabel = bchaddr.toCashAddress(CryptoWoo.payment_address).replace(/bitcoincash:/g,'');
            var cashAddressWithLabel = jQuery("#cw-qr-wrap a").attr("href").replace(currentAddress,cashAddressWithoutLabel);

            jQuery("#payment-address").text(cashAddressWithoutLabel);
            jQuery(".cw-tooltip:nth-child(2)").attr("href", cashAddressWithLabel);
            jQuery("#cw-qr-wrap a").attr("href", cashAddressWithLabel);

            new QRCode(document.getElementById("qrcode"), {
                text: cashAddressWithLabel,
                width: 250,
                height: 250,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        }
    }




});

//Called dynamically
function change_addr_BCH() {
    //Remove qrcode
    jQuery('#qrcode').empty();

    //TODO: Add REGEX to extract/edit only the address part of addressData?
    //TODO: Use newly added "currency" data added in CryptoWoo JSON and make address format based on that
    var currentPaymentAddress = bchaddr.toCashAddress(CryptoWoo.payment_address);
    var addr_format_button = jQuery("#change_addr_format");
    if(sessionStorage.qr_payment_address){
        currentPaymentAddress = sessionStorage.qr_payment_address;
    }

    var isLegacy = bchaddr.isLegacyAddress(currentPaymentAddress);
    var isCashAddress = bchaddr.isCashAddress(currentPaymentAddress);

    var addressWithoutLabel = currentPaymentAddress;

    if(isLegacy){
        addressWithoutLabel = bchaddr.toCashAddress(currentPaymentAddress).replace(/bitcoincash:/g,'');
        addr_format_button.text("Change to legacy-address");
    }
    else if (isCashAddress){
        addressWithoutLabel = bchaddr.toLegacyAddress(currentPaymentAddress).replace(/bitcoincash:/g,'');
        addr_format_button.text("Change to cash-address");
    }
    sessionStorage.qr_payment_address = addressWithoutLabel;

    //Update send address
    var currentAddress = jQuery("#payment-address").text();
    var addressWithLabel = jQuery("#cw-qr-wrap a").attr("href").replace(currentAddress,addressWithoutLabel);

    jQuery("#payment-address").text(addressWithoutLabel);
    jQuery(".cw-tooltip:nth-child(2)").attr("href", addressWithLabel);
    jQuery("#cw-qr-wrap a").attr("href", addressWithLabel);

    //Add qrcode back
    new QRCode(document.getElementById("qrcode"), {
        text: addressWithLabel,
        width: 250,
        height: 250,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.M
    });




}