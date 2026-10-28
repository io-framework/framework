@model (array{label: string, hot: bool})
<span class="badge">{{ $model['label'] }}@if ($model['hot']) <i>hot</i> @endif</span>
