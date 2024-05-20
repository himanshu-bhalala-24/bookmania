<x-app-layout>
    <div class="container mt-4">
        @include('layouts.flash')
        <div class="row">
            @if (count($orders))
                @foreach ($orders as $order)
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <p class="mb-0">Order #{{$order->id}}</p>
                                <p class="mb-0">{{$order->created_at}}</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">{{count($order->orderBooks)}} item(s)</li>
                            <li class="list-group-item d-flex justify-content-between">
                                <p class="mb-0"><span class="badge text-bg-dark">Total</span> <strong>{{$order->total}}</strong></p>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="offcanvas" data-bs-target="#order-details-{{$order->id}}" aria-controls="order-details-{{$order->id}}">Show order</a>
                            </li>
                        </ul>
                    </div>

                    {{-- order details --}}
                    <div class="offcanvas offcanvas-start w-75" data-bs-scroll="true" tabindex="-1" id="order-details-{{$order->id}}" aria-labelledby="order-details-label-{{$order->id}}">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="order-details-label-{{$order->id}}">Order #{{$order->id}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div class="card mb-4 col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Name:</strong> {{$order->name}}</li>
                                    <li class="list-group-item"><strong>Email:</strong> {{$order->email}}</li>
                                    <li class="list-group-item"><strong>Address:</strong> {{$order->address}}</li>
                                    <li class="list-group-item"><strong>Date:</strong> {{$order->created_at}}</li>
                                </ul>
                            </div>

                            {{-- order details --}}
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-nowrap" scope="col">#</th>
                                        <th scope="col">Book</th>
                                        <th class="text-nowrap" scope="col" width="200">Quantity</th>
                                        <th class="text-nowrap" scope="col" width="100">Price</th>
                                        <th class="text-nowrap" scope="col" width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderBooks as $orderBook)
                                    <tr>
                                        <th class="text-nowrap">{{$loop->iteration}}</th>
                                        <td><strong>{{$orderBook->book->name}}</strong> <i>({{$orderBook->book->category->name}})</i> - <strong>{{$orderBook->book->author}}</strong> <br> {{$orderBook->book->description}}</td>
                                        <td>{{$orderBook->quantity}}</td>
                                        <td class="text-nowrap">{{$orderBook->book->price}}</td>
                                        <td class="text-nowrap">{{$orderBook->book->price * $orderBook->quantity}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="col-md-12">
                                <div class="d-flex flex-row-reverse">
                                    <h5><strong>{{$order->total}}</strong> <span class="badge text-bg-dark">Total</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-md-12">
                    <h2>No orders available...</h2>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
