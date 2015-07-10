@extends('main')

@section('content')

<div class="row">
    <h2>iPhone Model Breakdown</h2>
    <hr>
</div>

{{-- @foreach ($years as $year) --}}
<div class="row">
    <h2>2015</h2>

    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Jan</th>
              <th>Feb</th>
              <th>Mar</th>
              <th>Apr</th>
              <th>May</th>
              <th>Jun</th>
              <th>Jul</th>
              <th>Aug</th>
              <th>Sept</th>
              <th>Oct</th>
              <th>Nov</th>
              <th>Dec</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($_2015 as $model => $month)
          <tr>
            <td>{{ $model }}</td>
            <td>{{ $month["January"] }}</td>
            <td>{{ $month["February"] }}</td>
            <td>{{ $month["March"] }}</td>
            <td>{{ $month["April"] }}</td>
            <td>{{ $month["May"] }}</td>
            <td>{{ $month["June"] }}</td>
            <td>{{ $month["July"] }}</td>
            <td>{{ $month["August"] }}</td>
            <td>{{ $month["September"] }}</td>
            <td>{{ $month["October"] }}</td>
            <td>{{ $month["November"] }}</td>
            <td>{{ $month["December"] }}</td>
          </tr>
          @endforeach
          </tbody>
    </table>
</div>

<div class="row">
    <h2>2014</h2>

    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Jan</th>
              <th>Feb</th>
              <th>Mar</th>
              <th>Apr</th>
              <th>May</th>
              <th>Jun</th>
              <th>Jul</th>
              <th>Aug</th>
              <th>Sept</th>
              <th>Oct</th>
              <th>Nov</th>
              <th>Dec</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($_2014 as $model => $month)
          <tr>
            <td>{{ $model }}</td>
            <td>{{ $month["January"] }}</td>
            <td>{{ $month["February"] }}</td>
            <td>{{ $month["March"] }}</td>
            <td>{{ $month["April"] }}</td>
            <td>{{ $month["May"] }}</td>
            <td>{{ $month["June"] }}</td>
            <td>{{ $month["July"] }}</td>
            <td>{{ $month["August"] }}</td>
            <td>{{ $month["September"] }}</td>
            <td>{{ $month["October"] }}</td>
            <td>{{ $month["November"] }}</td>
            <td>{{ $month["December"] }}</td>
          </tr>
          @endforeach
          </tbody>
    </table>
</div>

<div class="row">
    <h2>2013</h2>

    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Jan</th>
              <th>Feb</th>
              <th>Mar</th>
              <th>Apr</th>
              <th>May</th>
              <th>Jun</th>
              <th>Jul</th>
              <th>Aug</th>
              <th>Sept</th>
              <th>Oct</th>
              <th>Nov</th>
              <th>Dec</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($_2013 as $model => $month)
          <tr>
            <td>{{ $model }}</td>
            <td>{{ $month["January"] }}</td>
            <td>{{ $month["February"] }}</td>
            <td>{{ $month["March"] }}</td>
            <td>{{ $month["April"] }}</td>
            <td>{{ $month["May"] }}</td>
            <td>{{ $month["June"] }}</td>
            <td>{{ $month["July"] }}</td>
            <td>{{ $month["August"] }}</td>
            <td>{{ $month["September"] }}</td>
            <td>{{ $month["October"] }}</td>
            <td>{{ $month["November"] }}</td>
            <td>{{ $month["December"] }}</td>
          </tr>
          @endforeach
          </tbody>
    </table>
</div>

<div class="row">
    <h2>2012</h2>

    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Jan</th>
              <th>Feb</th>
              <th>Mar</th>
              <th>Apr</th>
              <th>May</th>
              <th>Jun</th>
              <th>Jul</th>
              <th>Aug</th>
              <th>Sept</th>
              <th>Oct</th>
              <th>Nov</th>
              <th>Dec</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($_2012 as $model => $month)
          <tr>
            <td>{{ $model }}</td>
            <td>{{ $month["January"] }}</td>
            <td>{{ $month["February"] }}</td>
            <td>{{ $month["March"] }}</td>
            <td>{{ $month["April"] }}</td>
            <td>{{ $month["May"] }}</td>
            <td>{{ $month["June"] }}</td>
            <td>{{ $month["July"] }}</td>
            <td>{{ $month["August"] }}</td>
            <td>{{ $month["September"] }}</td>
            <td>{{ $month["October"] }}</td>
            <td>{{ $month["November"] }}</td>
            <td>{{ $month["December"] }}</td>
          </tr>
          @endforeach
          </tbody>
    </table>
</div>

<div class="row">
    <h2>2011</h2>

    <table class="table table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Jan</th>
              <th>Feb</th>
              <th>Mar</th>
              <th>Apr</th>
              <th>May</th>
              <th>Jun</th>
              <th>Jul</th>
              <th>Aug</th>
              <th>Sept</th>
              <th>Oct</th>
              <th>Nov</th>
              <th>Dec</th>
            </tr>
          </thead>
          <tbody>

          @foreach ($_2011 as $model => $month)
          <tr>
            <td>{{ $model }}</td>
            <td>{{ $month["January"] }}</td>
            <td>{{ $month["February"] }}</td>
            <td>{{ $month["March"] }}</td>
            <td>{{ $month["April"] }}</td>
            <td>{{ $month["May"] }}</td>
            <td>{{ $month["June"] }}</td>
            <td>{{ $month["July"] }}</td>
            <td>{{ $month["August"] }}</td>
            <td>{{ $month["September"] }}</td>
            <td>{{ $month["October"] }}</td>
            <td>{{ $month["November"] }}</td>
            <td>{{ $month["December"] }}</td>
          </tr>
          @endforeach
          </tbody>
    </table>
</div>
{{-- @endforeach --}}

@stop

@section('scripts')
@stop
