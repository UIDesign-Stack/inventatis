@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{route('categories.update',$categorie->id)}}" method="post">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" name="name" id="" value="{{$categorie->name}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Description</label>
                            <input type="text" name="description" id="" value="{{$categorie->description}}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="py-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('categories.index')}}" class="btn btn-secondary">back</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
