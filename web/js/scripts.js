/*$(document).ready(function() {
    $('select').material_select();
});
*/

/*
$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});
*/


(function($){
    $(function(){

        $('.button-collapse').sideNav();
        $('.parallax').parallax();

    }); // end of document ready
})(jQuery); // end of jQuery name space

$(document).ready(function() {
    $('select').material_select();
});

$('.datepicker').on('focusin', function () {
    $('.datepicker').pickadate('open');
});

$('.datepicker').pickadate({
    selectMonths: true, // Creates a dropdown to control month
    selectYears: 15 // Creates a dropdown of 15 years to control year
});

$(document).ready(function(){
    $('tr.clickable').click(function(){
        window.location = $(this).data('href');
        return false;
    });
});


function openMaModal(){
    $('#modal_top').ready($('#modal_top').openModal());
}

$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});

function submitSearch(){

    $('button.hidden').click();

}