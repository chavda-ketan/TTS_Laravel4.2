	{{-- <pre>{{ var_dump($customers) }} </pre> --}}
<!DOCTYPE html>
<html>
<head>
	<title>Test Page</title>

	<style type="text/css">

p {
width: 400px;
color:blue;
display: inline-block;
}
.test1 {
	float:left;
}
.test2 {
	float:left;
}
.test3 {
	float:left;
}
.clear {
	clear:both;
}


	</style>

</head>

<body>

<p class="test1">
@foreach($customers as $customer)
	{{ $customer->firstName }}
	<br />
@endforeach
</p>
<p class="test2">
@foreach($customers as $customer)
	{{ $customer->lastName }}
	<br />
@endforeach
</p>
<p class="test3">
@foreach($customers as $customer)
	{{ $customer->city }}
	<br />
@endforeach
</p>
</body>
</html>


