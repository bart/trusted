@extends('template')

@section('content')
	<h1>Users</h1>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Username</th>
				<th>Domains</th>
				<th>Admin?</th>
				<th></th>
			</tr>
		</thead>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->username }}</td>
					<td>{{ $user->getDomainsCommaSeparated() }}</td>
					<td>
						@if($user->isAdmin())
							<i class="fa fa-check"></i>
						@else
							<i class="fa fa-times"></i>
						@endif
					</td>
					<td class="text-right">
							<button type="button" class="btn btn-default btn-xs editUserBtn" data-user-id="{{ $user->id }}" data-toggle="modal" data-target="#editUserModal"><i class="fa fa-pencil"></i></button>
						@if(Auth::user()->id != $user->id)
							<a href="{{ route('remove-user-path', $user->id) }}" class="btn btn-default btn-xs"><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
			@endforeach
		<tbody>
		</tbody>
	</table>

	<h2 class="mt100">Create</h2>
	{{ Form::open(['route' => 'create-user-path', 'class' => 'form-horizontal', 'role' => 'form']) }}
		<div class="form-group">
			{{ Form::label('username', 'Username', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('username', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::password('password', ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('domains', 'Domains', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::text('domains', null, ['class' => 'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('isAdmin', 'Admin', ['class' => 'col-sm-2 control-label']) }}
			<div class="col-sm-10">
				{{ Form::checkbox('isAdmin', true, false) }}
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				{{ Form::submit('Create', ['class' => 'btn btn-success']) }}
			</div>
		</div>
	{{ Form::close() }}

	<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				{{ Form::open(['route' => 'edit-user-path', 'class' => 'form-horizontal', 'role' => 'form']) }}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
						<h4 class="modal-title">Edit user</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							{{ Form::label('edit-username', 'Username', ['class' => 'col-sm-2 control-label']) }}
							<div class="col-sm-10">
								{{ Form::text('edit-username', null, ['class' => 'form-control', 'disabled']) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('edit-password', 'Password', ['class' => 'col-sm-2 control-label']) }}
							<div class="col-sm-10">
								{{ Form::password('edit-password', ['class' => 'form-control']) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('edit-domains', 'Domains', ['class' => 'col-sm-2 control-label']) }}
							<div class="col-sm-10">
								{{ Form::text('edit-domains', null, ['class' => 'form-control']) }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('edit-isAdmin', 'Admin', ['class' => 'col-sm-2 control-label']) }}
							<div class="col-sm-10">
								{{ Form::checkbox('edit-isAdmin', true, false) }}
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-success">Save</button>
					</div>
					{{ Form::hidden('edit-userId', null, ['id' => 'edit-userId']) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop

@section('js')
	{{ HTML::script('js/require.js', ['data-main' => 'js/init', 'data-module' => 'users']) }}
@stop