<!DOCTYPE html>
<html>
<head>
    <title>{{$type ? 'Clinician Learner Link' : 'Learner Link Created'}}</title>
</head>
<body>
    <p>Hi {{ $data['name'] }},</p>
    @if($type)
        <p>{{ $data['message'] }} <b>{{$data['learner_name']}}</b></p>
    @else
        <p>{{ $data['message'] }} <b>{{$data['code']}}</b></p>
    @endif
</body>
</html>