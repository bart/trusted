@extends('template')

@section('content')
	<h1>Root CA</h1>

	{{ Form::open(['route' => 'create-root-ca-path', 'class' => 'form-horizontal', 'role' => 'form']) }}
		<div class="form-group">
			{{ Form::label('c', 'Country', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::select('c', Config::get('countries'), 'DE', ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('st', 'State', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('st', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('l', 'Locality', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('l', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('o', 'Organization', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('o', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('ou', 'Unit', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('ou', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('cn', 'Common name (FQDN)', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('cn', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('email', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit('Create Root CA', ['class' => 'btn btn-success']) }}
			</div>
		</div>
	{{ Form::close() }}
@stop

@section('js')
	{{ HTML::script('js/require.js', ['data-main' => 'js/init']) }}
@stop