const toastAlertElem = $('#toast-alert');
const toastAlert = bootstrap.Toast.getOrCreateInstance(toastAlertElem);

$(document).on('click', '.quantity-change', function () {
    const _this = $(this);
    const id = _this.data('book-id');
    const actionUrl = "{{route('cart.quantity')}}";
    const isIncrease = _this.attr('data-increase') === 'true';

    $.ajax({
        type: 'POST',
        url: actionUrl,
        contentType: 'application/json',
        data: JSON.stringify({
            _token: '{{ csrf_token() }}',
            id: id,
            isIncrease: isIncrease
        }),
        success: function (res) {
            if (res.success) {
                const quantityElem = $(`.quantity-${id}`);
                const quantity = parseInt(quantityElem.val());

                if (isIncrease) {
                    quantityElem.val(quantity + 1);
                    
                    if (quantity == 1) {
                        $(`#decrease-quantity-${id}`).prop('disabled', false);
                    }
                } else {
                    if (quantity > 1) {
                        quantityElem.val(quantity - 1);

                        if (quantity == 2) {
                            quantityElem.val(quantity - 1);
                            _this.prop('disabled', true);
                        }
                    }
                }

                // for cart page
                if ($('#cart-table').length > 0) {
                    $(`#book-total-${id}`).text((quantityElem.data('book-price') * parseInt(quantityElem.val())).toFixed(2));
                    changeTotal();
                }
            } else {
                $('#toast-msg').text(res.message);
                toastAlert.show();
            }
        },
        error: function (err) {
            alert('Something went wrong');
        }
    });
});