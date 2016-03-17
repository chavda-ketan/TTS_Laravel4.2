@extends('main')

@section('content')

<div class="row">
    <h2>Add Business Account</h2>

    <form class="form-horizontal" method="post" action="add">
        <fieldset>
            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Company</span>
                        <input name="Company" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Address</span>
                        <input name="Address" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">City</span>
                        <input name="City" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Postal Code</span>
                        <input name="Postal" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Admin Phone</span>
                        <input name="Phone" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Admin Email</span>
                        <input name="Email" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Name</span>
                        <input name="CustomText1" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Phone</span>
                        <input name="CustomText2" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">AP Email</span>
                        <input name="Email2" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Credit Limit</span>
                        <input name="CreditLimit" type="text" value="" class="form-control input-md">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>

        </fieldset>
    </form>

</div>

@stop

@section('scripts')

@stop
