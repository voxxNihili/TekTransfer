<!DOCTYPE html>
<html>
<head>
 <title>Mail</title>
</head>
<body>
 
 <h1>{{ $details['title'] }}</h1>
 <p>Sayın, {{ $details['name'] }}<br>{{ $details['orderCode'] }} {{ $details['body'] }}</p>
 <p>Sisteme girişinizi <b>{{ $details['key'] }}</b> ürün anahtarı ile sağlayabilirsiniz.</p>
 <p>Sorun yaşamanız halinde <b>info@teksenbilisim.com</b> mail adresinden bize ulaşabilirsiniz.</p>
 
</body>
</html> 