@extends('main')

@section('content')

<div class="row">
    <h2>Editing {{ $customer->Company }} Accounts Payable</h2>

    <form class="form-horizontal" method="post" action="{{ $customer->ID }}/save">
        <fieldset>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Name</span>
                        <input name="CustomText1" type="text" value="{{ $customer->CustomText1 }}" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Phone</span>
                        <input name="CustomText2" type="text" value="{{ $customer->CustomText2 }}" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Email</span>
                        <input name="Email2" type="text" value="{{ $customer->Email2 }}" class="form-control input-md">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>

        </fieldset>
    </form>

</div>

@stop

@section('scripts')

@stop
