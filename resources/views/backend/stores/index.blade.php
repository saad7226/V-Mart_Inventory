@extends('backend.master')

@section('title', 'Store Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-store me-2 text-primary"></i>
            All Stores
            <span class="badge bg-primary ms-2">{{ $stores->count() }} Total</span>
        </h5>
        <small class="text-muted">Super Admin View — All tenant stores</small>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Store Name</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Users</th>
                        <th>Created</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary">{{ $store->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;font-size:14px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($store->store_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $store->store_name }}</div>
                                    @if($store->address)
                                        <small class="text-muted">{{ $store->address }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium">{{ $store->owner_name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <a href="mailto:{{ $store->owner_email }}" class="text-decoration-none text-muted small">
                                {{ $store->owner_email ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ $store->product_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $store->order_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">{{ $store->user_count }}</span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($store->created_at)->format('d M, Y') }}
                            </small>
                        </td>
                        <td class="text-center">
                            @if($store->id !== 1)
                            <form action="{{ route('backend.admin.stores.destroy', $store->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this store and unlink all its users? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @else
                                <span class="badge bg-secondary">Primary</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-store fa-2x mb-2 d-block opacity-50"></i>
                            No stores found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
