@extends('backend.master')

@section('title', 'Products')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title font-weight-bold">Product Catalog</h3>
    @can('product_create')
    <a href="{{ route('backend.admin.products.create') }}" class="btn btn-primary ml-auto">
      <i class="fas fa-plus-circle mr-1"></i> Add Product
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
                <th></th>
                <th>Name</th>
                <th>Price{{currency()->symbol??''}}</th>
                <th>Stock</th>
                <th>Created</th>
                <th>Status</th>
                <th data-orderable="false">Action</th>
              </tr>
            </thead>
          </table>
          <!-- Pagination Links -->
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
        url: "{{ route('backend.admin.products.index') }}"
      },

      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'image',
          name: 'image'
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'price',
          name: 'price'
        },
        {
          data: 'quantity',
          name: 'quantity'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'status',
          name: 'status'
        },
        {
          data: 'action',
          name: 'action'
        },
      ]
    });

    // Auto-Sync (Reload table every 10 seconds)
    setInterval(function() {
      table.ajax.reload(null, false); // false means keep the current paging
    }, 10000);
  });
</script>
@endpush