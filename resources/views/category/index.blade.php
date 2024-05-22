<x-app-layout>
    <div class="container mt-4">
        @include('layouts.flash')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex flex-row-reverse">
                    <a type="button" class="btn btn-secondary mb-2" href="{{ route('category.create') }}">Add category</a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-nowrap" scope="col">#</th>
                            <th class="text-nowrap" scope="col">Name</th>
                            <th class="text-nowrap" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $category)
                        <tr>
                            <th class="text-nowrap">{{$loop->iteration}}</th>
                            <td class="text-nowrap">{{$category->name}}</td>
                            <td>
                                <div class="d-flex justify-content-evenly">
                                    {{-- edit --}}
                                    <a href="{{route('category.edit', $category->id)}}" class="text-decoration-none text-primary">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if ($category->trashed())
                                    {{-- restore --}}
                                    <a type="button" class="text-decoration-none text-primary restore-category-btn" data-id="{{$category->id}}">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                    @else
                                    {{-- delete --}}
                                    <a type="button" class="text-decoration-none text-primary delete-category-btn" data-id="{{$category->id}}">
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
    
        {{-- Delete category modal --}}
        <div class="modal fade" id="delete-category-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-category-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="delete-category-modal-label">Are you sure you want to delete this category?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form id="delete-category-form" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Restore category modal --}}
        <div class="modal fade" id="restore-category-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="restore-category-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="restore-category-modal-label">Are you sure you want to restore this category?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <form id="restore-category-form" method="POST">
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
            // delete category
            const deleteCategoryRoute = "{{route('category.destroy', ':id')}}";
            const restoreCategoryRoute = "{{route('category.restore', ':id')}}";
    
            $('.delete-category-btn').click(function() {
                const id = $(this).data('id');
    
                $('#delete-category-form').attr('action', deleteCategoryRoute.replace(':id', id));
                $('#delete-category-modal').modal('show');
            });

            $('.restore-category-btn').click(function() {
                const id = $(this).data('id');
    
                $('#restore-category-form').attr('action', restoreCategoryRoute.replace(':id', id));
                $('#restore-category-modal').modal('show');
            });
        });
    </script>
    @endsection
</x-app-layout>
