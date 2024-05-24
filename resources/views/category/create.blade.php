<x-app-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('layouts.flash')
    
                @include('common.validation')
    
                <form method="POST" action="{{ route('category.store') }}">
                    @csrf
    
                    {{-- category --}}
                    <div class="mt-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{old('category')}}" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
