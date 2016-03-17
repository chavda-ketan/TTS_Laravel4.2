<!DOCTYPE html>
<html>
<head>
	<title>Test Page</title>

	<style type="text/css">

body {
	font-size: 75%;
}

	</style>
</head>

<body>
		<form method="post" action="inputtest">
			First Name<input type="text" name="first" value="{{ $firstName }}"> <br />
			Last Name<input type="text" name="last" value="{{ $lastName }}">
			<input type="submit" value="Submit form">

		</form>

{{ $firstName }}
{{ $lastName }}
</body>
</html>


