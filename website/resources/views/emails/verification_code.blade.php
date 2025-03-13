<!DOCTYPE html>
<html>
<head>
    <title>Login Verification Code</title>
</head>
<body>
    <p>Hi {{ $data['name'] }},</p>
    <p>{{ $data['message'] }} <b>{{$data['code']}}</b></p>
</body>
</html>