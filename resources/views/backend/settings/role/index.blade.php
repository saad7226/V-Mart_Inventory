@extends('backend.master')

@section('title', 'Roles')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">User Roles</h3>
        @can('role_create')
        <button class="btn btn-primary ml-auto" data-toggle="modal" data-target="#roleModal">
            <i class="fas fa-plus-circle mr-1"></i> Add Role
        </button>
        @endcan
    </div>
    <div class="card-body">
        <!-- Modal -->
        <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                {!! Form::open(['url' => route('backend.admin.roles.create'), 'method' => 'post']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Add New Role
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Role Name']) !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                        <tr>
                            <td> {{ $role->name }} </td>
                            <td>
                                <div class="text-center">
                                    <a title="Permission Setup"
                                        href="{{ route('backend.admin.roles.show', $role->id) }}" type="button"
                                        class="btn btn-dark btn-xs">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                    @if ($role->id != 1)
                                    <button title="Edit Role" type="button" class="btn btn-primary btn-xs"
                                        data-toggle="modal" data-target="#editRole-{{ $role->id }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <a title="Delete Role"
                                        href="{{ route('backend.admin.roles.delete', $role->id) }}"
                                        type="button" class="btn btn-danger btn-xs"
                                        onclick="return confirm('Are you sure ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="editRole-{{ $role->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        {!! Form::open(['method' => 'put', 'route' => ['backend.admin.roles.update', $role->id]]) !!}
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fs-5" id="exampleModalLabel">
                                                    <i class="fas fa-pencil-alt"></i>
                                                    Edit Role
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="control-label">Name:</label>
                                                    {!! Form::text('name', $role->name, ['class' => 'form-control', 'placeholder' => 'Role Name']) !!}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    Save changes
                                                </button>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection