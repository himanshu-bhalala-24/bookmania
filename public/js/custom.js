$(function() {
    setTimeout(function() {
        $('.flash-msg').fadeOut('slow');
    }, 10000);

    // select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });
});