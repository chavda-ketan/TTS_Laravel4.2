@extends('main')

@section('content')
<h2>Add Category</h2>

<form method="post" action="supplier" class="form-horizontal" role="form">
    <div class="col-sm-6">

        <div class="form-group">
            <input class="form-control" name="country" placeholder="Country">
        </div>

        <div class="form-group">
            <input class="form-control" name="state" placeholder="State">
        </div>

        <div class="form-group">
            <input class="form-control" name="suppliername" placeholder="Supplier Name">
        </div>

        <div class="form-group">
            <input class="form-control" name="contactname" placeholder="Contact Name">
        </div>

        <div class="form-group">
            <input class="form-control" name="address1" placeholder="Address 1">
        </div>

        <div class="form-group">
            <input class="form-control" name="address2" placeholder="Address 2">
        </div>

        <div class="form-group">
            <input class="form-control" name="city" placeholder="City">
        </div>

        <div class="form-group">
            <input class="form-control" name="zip" placeholder="Zip">
        </div>

        <div class="form-group">
            <input class="form-control" name="email" placeholder="Email">
        </div>

        <div class="form-group">
            <input class="form-control" name="site" placeholder="URL">
        </div>

        <div class="form-group">
            <input class="form-control" name="code" placeholder="Code">
        </div>

        <div class="form-group">
            <input class="form-control" name="phone" placeholder="Phone #">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-default">Add</button>
        </div>

    </div>
</form>
@stop



@section('scripts')

@stop
