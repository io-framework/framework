@model (array{status: int, title: string, detail: ?string})
<h1>error page {{ $model['status'] }} {{ $model['title'] }}</h1>
@if ($model['detail'] !== null)
<p>{{ $model['detail'] }}</p>
@endif
