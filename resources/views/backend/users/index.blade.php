@extends('backend.master')

@section('title', 'User Management')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title font-weight-bold">System Users</h3>
    @can('user_create')
    <a href="{{ route('backend.admin.user.create') }}" class="btn btn-primary ml-auto">
      <i class="fas fa-user-plus mr-1"></i> Add User
    </a>
    @endcan
  </div>
  <div class="card-body p-0">
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card-body table-responsive p-0" id="table_data">
                    <table id="datatables" class="table table-hover">
                        <thead>
                            <tr>
                                <th data-orderable="false">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th data-orderable="false">
                                    Action
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(function() {
        let table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],
            ajax: {
                url: "{{ route('backend.admin.users') }}"
            },

            columns: [{
                    data: 'thumb',
                    name: 'thumb',
                    searchable: false,
                    orderable: false
                }, {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'roles',
                    name: 'roles'
                },
                {
                    data: 'created',
                    name: 'created'
                },
                {
                    data: 'suspend',
                    name: 'suspend',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'approval',
                    name: 'approval',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                },
            ]
        });
    });
</script>
@endpush