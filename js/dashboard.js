/**
 * Created by subham on 1/6/17.
 */
var URL="http://13.228.26.230/";
$.get( URL+"getDashboard")
    .done(function( value ) {
        var data=value.value
        $('#active').html(data.users);
        $('#courier').html(data.drivers);
        $('#booking').html(data.booking);
        $('#currentTrip').html(data.currentTrip);
        $('#promo').html(data.promo);
        $('#dispute').html(data.dispute);
        $('#payout').html(data.payout);
        $('#cWallet').html(data.cWallet);
        $('#sWallet').html(data.sWallet);
    })
