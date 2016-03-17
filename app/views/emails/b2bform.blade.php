@extends('main')

@section('content')

<div class="row">
    <h2>Send B2B Email</h2>

    <form class="form-horizontal" method="post" action="">
        <fieldset>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Name</span>
                        <input name="name" type="text" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Email Address</span>
                        <input name="email" type="text" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>

        </fieldset>
    </form>

</div>

@stop

@section('scripts')

@stop
