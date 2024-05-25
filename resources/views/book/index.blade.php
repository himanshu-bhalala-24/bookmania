<x-app-layout>
    <div class="container mt-4">
        @include('layouts.flash')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-row-reverse">
                    <a type="button" class="btn btn-secondary mb-2" href="{{ route('book.create') }}">Add book</a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-nowrap" scope="col">#</th>
                            <th class="text-nowrap" scope="col">Image</th>
                            <th class="text-nowrap" scope="col">Name</th>
                            <th class="text-nowrap" scope="col">Category</th>
                            <th class="text-nowrap" scope="col">Author</th>
                            <th class="text-nowrap" scope="col">Price</th>
                            <th class="text-nowrap" scope="col">Quantity</th>
                            <th class="text-nowrap" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $book)
                        <tr>
                            <td class="text-nowrap">{{$loop->iteration}}</td>
                            <td class="text-nowrap"><img src="{{\Storage::disk('public')->url('books/' . $book->image)}}" alt="image" width="70"></td>
                            <td class="text-nowrap">{{$book->name}}</td>
                            <td class="text-nowrap">{{$book->category->name}}</td>
                            <td class="text-nowrap">{{$book->author}}</td>
                            <td class="text-nowrap">{{$book->price}}</td>
                            <td class="text-nowrap">{{$book->quantity}}</td>
                            <td>
                                <div class="d-flex justify-content-evenly">
                                    {{-- edit --}}
                                    <a href="{{route('book.edit', $book->id)}}" class="text-decoration-none text-primary">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if ($book->trashed())
                                    {{-- restore --}}
                                    <a type="button" class="text-decoration-none text-primary restore-book-btn" data-id="{{$book->id}}">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                    @else
                                    {{-- delete --}}
                                    <a type="button" class="text-decoration-none text-primary delete-book-btn" data-id="{{$book->id}}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $data->links() }}
            </div>
        </div>
    
        {{-- Delete book modal --}}
        <div class="modal fade" id="delete-book-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-book-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="delete-book-modal-label">Are you sure you want to delete this book?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form id="delete-book-form" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Restore book modal --}}
        <div class="modal fade" id="restore-book-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="restore-book-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="restore-book-modal-label">Are you sure you want to restore this book?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form id="restore-book-form" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('page-script')
    <script>
        $(function() {
            // delete book
            const deleteBookRoute = "{{route('book.destroy', ':id')}}";
            const restoreBookRoute = "{{route('book.restore', ':id')}}";
    
            $('.delete-book-btn').click(function() {
                const id = $(this).data('id');
    
                $('#delete-book-form').attr('action', deleteBookRoute.replace(':id', id));
                $('#delete-book-modal').modal('show');
            });

            $('.restore-book-btn').click(function() {
                const id = $(this).data('id');
    
                $('#restore-book-form').attr('action', restoreBookRoute.replace(':id', id));
                $('#restore-book-modal').modal('show');
            });
        });
    </script>
    @endsection
</x-app-layout>
