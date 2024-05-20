$(document).on('click', '.quantity-change', function () {
    const id = $(this).data('book-id');
    const actionUrl = "{{route('cart.quantity')}}";
    const isIncrease = $(this).attr('data-increase') === 'true';

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
            const quantityElem = $(`.quantity-${id}`);
            const quantity = parseInt(quantityElem.val());

            if (isIncrease) {
                quantityElem.val(quantity + 1);
            } else {
                if (quantity > 1) {
                    quantityElem.val(quantity - 1);
                }
            }

            // for cart page
            if ($('#cart-table').length > 0) {
                $(`#book-total-${id}`).text(quantityElem.data('book-price') * parseInt(quantityElem.val()));
                changeTotal();
            }
        },
        error: function (err) {
            alert('Something went wrong');
        }
    });
});