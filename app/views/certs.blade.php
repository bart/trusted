@extends('template')

@section('content')
	<h1>Certs</h1>
	@if(count($certs))
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Domain</th>
					@if(Auth::user()->isAdmin())
						<th>Owner</th>
					@endif
					<th></th>
				</tr>
			</thead>
				@foreach($certs as $cert)
					<tr>
						<td>{{ $cert->domain }}</td>
						@if(Auth::user()->isAdmin())
							<td>{{ $cert->owner->username }}</td>
						@endif
						<td class="text-right">
							<button type="button" class="btn btn-default btn-xs showCertBtn" data-cert-id="{{ $cert->id }}" data-toggle="modal" data-target="#showCertModal"><i class="fa fa-certificate"></i></button>
							@if($cert->csr == 0)
								<button type="button" class="btn btn-default btn-xs showKeyBtn" data-cert-id="{{ $cert->id }}" data-toggle="modal" data-target="#showKeyModal"><i class="fa fa-key"></i></button>
							@endif
							<a href="{{ route('remove-cert-path', $cert->id) }}" class="btn btn-default btn-xs"><i class="fa fa-trash"></i></a>
						</td>
					</tr>
				@endforeach
			<tbody>
			</tbody>
		</table>
	@endif

	<h2 class="mt100">Create</h2>
	{{ Form::open(['route' => 'create-cert-path', 'class' => 'form-horizontal', 'role' => 'form']) }}
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
			<div class="col-sm-7">
				{{ Form::text('cn', null, ['class' => 'form-control']) }}
			</div>
			<div class="col-sm-3">
				{{ Form::select('cns', Auth::user()->getDomainsWithoutAsterisk(), null, ['class' => 'form-control']) }}
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
				{{ Form::submit('Create signed certificate', ['class' => 'btn btn-success']) }}
			</div>
		</div>
	{{ Form::close() }}

	<h2 class="mt100">Sign</h2>
    	{{ Form::open(['route' => 'sign-cert-path', 'class' => 'form-horizontal', 'role' => 'form', 'files' => true]) }}
    		<div class="form-group">
    			{{ Form::label('csr', 'CSR', ['class' => 'col-sm-2 control-label']) }}
    			<div class="col-sm-10">
    				{{ Form::file('csr') }}
    			</div>
    		</div>
    		<div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
    				{{ Form::submit('Sign the CSR', ['class' => 'btn btn-success']) }}
    			</div>
    		</div>
    	{{ Form::close() }}

	<div class="modal fade" id="showCertModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
					<h4 class="modal-title">Show certificate</h4>
				</div>
				<div class="modal-body" id="certificate-wrapper">

				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-default btn-xs" id="downloadCertLink"><i class="fa fa-download"></i></a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="showKeyModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
					<h4 class="modal-title">Show private key</h4>
				</div>
				<div class="modal-body" id="key-wrapper">

				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-default btn-xs" id="downloadKeyLink"><i class="fa fa-download"></i></a>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
	{{ HTML::script('js/require.js', ['data-main' => 'js/init', 'data-module' => 'certs']) }}
@stop