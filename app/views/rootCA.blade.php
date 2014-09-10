@extends('template')

@section('content')
	<h1>Root CA</h1>

	<a class="btn btn-primary btn-sm" href="{{ route('download-public-root-ca-cert-path') }}">Download public Root CA certificate</a>
	<a class="btn btn-primary btn-sm" href="{{ route('download-public-root-ca-key-path') }}">Download public Root CA private key</a>
	<a class="btn btn-danger btn-sm" href="{{ route('remove-root-ca-path') }}">Remove Root CA certificate & private key</a>

	<pre class="mt20">{{ $cert }}</pre>
@stop

@section('js')
	{{ HTML::script('js/require.js', ['data-main' => 'js/init']) }}
@stop