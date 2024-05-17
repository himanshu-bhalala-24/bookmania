<x-app-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('layouts.flash')
    
                @if ($errors->any())
                <div class="alert alert-danger flash-msg">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
    
                <form method="POST" action="{{ route('book.update', $book->id) }}">
                    @csrf
                    @method('PATCH')
    
                    {{-- category --}}
                    <div class="mt-3">
                        <label class="form-label">Category</label>
                        <select class="form-select select2" name="category" required>
                            <option selected disabled>Select category</option>
                            @foreach ($categories as $category)
                            <option value="{{$category->id}}" @selected(old('category') ? (old('category') == $category->id) : ($book->category_id == $category->id))>{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- book --}}
                    <div class="mt-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="book" class="form-control" value="{{old('book') ?? $book->name}}" required>
                    </div>


                    {{-- description --}}
                    <div class="mt-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required>{{old('description') ?? $book->description}}</textarea>
                    </div>

                    {{-- price --}}
                    <div class="mt-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" value="{{old('price') ?? $book->price}}" required>
                    </div>

                    {{-- author --}}
                    <div class="mt-3">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control" value="{{old('author') ?? $book->author}}" required>
                    </div>

                    
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
