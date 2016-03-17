	{{-- <pre>{{ var_dump($customers) }} </pre> --}}
<!DOCTYPE html>
<html>
<head>
	<title>Test Page</title>

	<style type="text/css">

body {
	font-size: 75%;
	background-color: #cccccc;
}
table {
	margin:auto;
	border: 1px solid #000000;	
	border-radius: 5px;
	width: auto;
	font-size: 1.2em;
	background: -moz-linear-gradient(left,  rgba(212,228,239,1) 0%, rgba(134,174,204,0.9) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(left,  rgba(212,228,239,1) 0%,rgba(134,174,204,0.9) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to right,  rgba(212,228,239,1) 0%,rgba(134,174,204,0.9) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d4e4ef', endColorstr='#e686aecc',GradientType=1 ); /* IE6-9 */

}
table tr:first-child {
	background: #d2dfed; /* Old browsers */
	background: -moz-linear-gradient(-45deg,  #d2dfed 0%, #c8d7eb 26%, #bed0ea 51%, #a6c0e3 51%, #afc7e8 62%, #bad0ef 75%, #99b5db 88%, #799bc8 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(-45deg,  #d2dfed 0%,#c8d7eb 26%,#bed0ea 51%,#a6c0e3 51%,#afc7e8 62%,#bad0ef 75%,#99b5db 88%,#799bc8 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(135deg,  #d2dfed 0%,#c8d7eb 26%,#bed0ea 51%,#a6c0e3 51%,#afc7e8 62%,#bad0ef 75%,#99b5db 88%,#799bc8 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d2dfed', endColorstr='#799bc8',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */

}
table tr {
	padding-left:5px;
}
table td {
	width:80px;
	text-align: center;
	border: 1px solid #000000;	
	border-radius: 2px;
}
	
	</style>


</head>

<body>

<table>
		<tr>
			<td><strong>Name</strong></td>
			<td><strong>Last Name</strong></td>
			<td><strong>City</strong></td>
		</tr>

	@foreach($customers as $customer)
		
		<tr>
			<td> {{ $customer->firstName }} </td>
			<td> {{ $customer->lastName }} </td>
			<td> {{ $customer->city }} </td>
		</tr>
	@endforeach
</table>

</body>
</html>


