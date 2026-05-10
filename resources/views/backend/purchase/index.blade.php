@extends('backend.master')

@section('title', 'Purchase')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title font-weight-bold">Purchase Log</h3>
    @can('purchase_create')
    <a href="{{ route('backend.admin.purchase.create') }}" class="btn btn-primary ml-auto">
      <i class="fas fa-plus-circle mr-1"></i> Add Purchase
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
                <th>Supplier</th>
                <th>ID</th>
                <th>Total {{currency()->symbol??''}}</th>
                <th>Date</th>
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
      ajax: {
        url: "{{ route('backend.admin.purchase.index') }}"
      },

      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'supplier',
          name: 'supplier'
        },

        {
          data: 'id',
          name: 'id'
        },
        {
          data: 'total',
          name: 'total',
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'action',
          name: 'action'
        },
      ]
    });
  });
</script>
@endpush