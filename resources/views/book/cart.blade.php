<x-app-layout>
    <div class="container mt-4">
        <div class="row">
            @if (count($books))
                <div class="col-md-12">
                    <div class="d-flex flex-row-reverse">
                        <a type="button" class="btn btn-danger mb-2" href="{{ route('cart.empty') }}">Empty cart</a>
                    </div>
                </div>
                
                <table class="table table-bordered table-hover" id="cart-table">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-nowrap" scope="col">#</th>
                            <th scope="col">Book</th>
                            <th class="text-nowrap" scope="col" width="200">Quantity</th>
                            <th class="text-nowrap" scope="col" width="100">Price</th>
                            <th class="text-nowrap" scope="col" width="100">Total</th>
                            <th class="text-nowrap" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                        <tr class="tr-{{$book->id}}">
                            <th class="text-nowrap">{{$loop->iteration}}</th>
                            <td><strong>{{$book->name}}</strong> <i>({{$book->category->name}})</i> - <strong>{{$book->author}}</strong> <br> {{$book->description}}</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger btn-number quantity-change" data-book-id="{{$book->id}}" data-increase="false">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </span>
                                    <input type="number" class="form-control quantity-{{$book->id}}" value="{{$cart[$book->id]}}" min="1" placeholder="Quantity" data-book-id="{{$book->id}}" data-book-price="{{$book->price}}" disabled />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success btn-number quantity-change" data-book-id="{{$book->id}}" data-increase="true">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td class="text-nowrap">{{$book->price}}</td>
                            <td class="text-nowrap book-total" id="book-total-{{$book->id}}">{{$book->price * $cart[$book->id]}}</td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-evenly">
                                    {{-- delete --}}
                                    <a type="button" class="text-decoration-none text-danger remove-book" data-book-id="{{$book->id}}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="col-md-12">
                    <div class="d-flex flex-row-reverse">
                        <h5><strong id="cart-total"></strong> <span class="badge text-bg-dark">Total</span></h5>
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <h2>Your cart is empty...</h2>
                </div>
            @endif
        </div>
    </div>

    @section('page-script')
    <script>
        $(function() {
            @include('common.cart-js')
            changeTotal();

            $(document).on('click', '.remove-book', function() {
                const id = $(this).data('book-id');
                const actionUrl = "{{route('cart.remove')}}";
                
                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function (res) {
                        if ($('#cart-table tbody').find('tr').length === 1) {
                            location.reload();
                        } else {
                            $(`.tr-${id}`).remove();
                        }

                        changeTotal();
                    },
                    error: function (err) {
                        alert('Something went wrong');
                    }
                });
            });

            function changeTotal() {
                let total = 0;

                $('.book-total').each(function() {
                    let value = parseFloat($(this).text());
                    total += value;
                });
                
                $('#cart-total').text(total);
            }
        });
    </script>
    @endsection
</x-app-layout>
