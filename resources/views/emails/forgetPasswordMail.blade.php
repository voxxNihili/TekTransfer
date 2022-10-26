<!DOCTYPE html>
<html>
<head>
 <title>Mail</title>
</head>
<body>
 
 <h1>{{ $details['title'] }}</h1>
 <p>Sayın, {{ $details['name'] }}<br>{{ $details['body'] }}</p>
 <p>Şifreniz : <b>{{ $details['password'] }}</b></p>
 
</body>
</html> 