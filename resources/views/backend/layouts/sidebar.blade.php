@php
$route = request()->route()->getName();
@endphp
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            {{-- ── Dashboard ──────────────────────────────────── --}}
            @can('dashboard_view')
            <li class="nav-item">
                <a href="{{ route('backend.admin.dashboard') }}"
                    class="nav-link {{ $route === 'backend.admin.dashboard' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-th-large"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            @endcan

            {{-- ── POS Terminal ────────────────────────────────── --}}
            @can('sale_create')
            <li class="nav-item">
                <a href="{{ route('backend.admin.cart.index') }}"
                    class="nav-link {{ $route === 'backend.admin.cart.index' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>POS</p>
                </a>
            </li>
            @endcan

            {{-- ── People (Customers & Suppliers) ─────────────── --}}
            @if (auth()->user()->hasAnyPermission([
                'customer_create', 'customer_view', 'customer_update', 'customer_delete', 'customer_sales',
                'supplier_create', 'supplier_view', 'supplier_update', 'supplier_delete',
            ]))
            <li class="nav-item {{ request()->routeIs(['backend.admin.customers.*', 'backend.admin.suppliers.*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.customers.*', 'backend.admin.suppliers.*']) ? 'active' : '' }}">
                    <i class="fas fa-user-friends nav-icon"></i>
                    <p>
                        People
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (auth()->user()->hasAnyPermission(['customer_view']))
                    <li class="nav-item">
                        <a href="{{route('backend.admin.customers.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.customers.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Customer</p>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->hasAnyPermission(['supplier_view']))
                    <li class="nav-item">
                        <a href="{{route('backend.admin.suppliers.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.suppliers.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Supplier</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- ── Product ─────────────────────────────────────── --}}
            @if (auth()->user()->hasAnyPermission([
                'product_create', 'product_view', 'product_update', 'product_delete', 'product_import', 'product_purchase',
                'category_view', 'brand_view', 'unit_view',
            ]))
            <li class="nav-item {{ request()->routeIs(['backend.admin.products.*', 'backend.admin.brands.*', 'backend.admin.categories.*', 'backend.admin.units.*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.products.*', 'backend.admin.brands.*', 'backend.admin.categories.*', 'backend.admin.units.*']) ? 'active' : '' }}">
                    <i class="fas fa-layer-group nav-icon"></i>
                    <p>
                        Product
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('product_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.products.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.products.index', 'backend.admin.products.edit']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Product List</p>
                        </a>
                    </li>
                    @endcan
                    @can('product_create')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.products.create')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.products.create']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Product Create</p>
                        </a>
                    </li>
                    @endcan
                    @can('product_import')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.products.import')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.products.import']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Product Import</p>
                        </a>
                    </li>
                    @endcan
                    @can('brand_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.brands.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.brands.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Brand</p>
                        </a>
                    </li>
                    @endcan
                    @can('category_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.categories.index')}}"
                            class="nav-link {{ request()->routeIs([ 'backend.admin.categories.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Category</p>
                        </a>
                    </li>
                    @endcan
                    @can('unit_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.units.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.units.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Unit</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            {{-- ── Sale ────────────────────────────────────────── --}}
            @if (auth()->user()->hasAnyPermission(['sale_view']))
            <li class="nav-item {{ request()->routeIs(['backend.admin.orders.*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.orders.*']) ? 'active' : '' }}">
                    <i class="fas fa-receipt nav-icon"></i>
                    <p>
                        Sale
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('sale_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.orders.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.orders.index']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sale List</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            {{-- ── Purchase ─────────────────────────────────────── --}}
            @if (auth()->user()->hasAnyPermission(['purchase_view', 'purchase_create']))
            <li class="nav-item {{ request()->routeIs(['backend.admin.purchase.*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.purchase.*']) ? 'active' : '' }}">
                    <i class="fas fa-truck-loading nav-icon"></i>
                    <p>
                        Purchase
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('purchase_view')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.purchase.index')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.purchase.index']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Purchase List</p>
                        </a>
                    </li>
                    @endcan
                    @can('purchase_create')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.purchase.create')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.purchase.create']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Purchase Create</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            {{-- ── Reports ──────────────────────────────────────── --}}
            @if (auth()->user()->hasAnyPermission(['reports_summary', 'reports_sales', 'reports_inventory']))
            <li class="nav-item {{ request()->routeIs(['backend.admin.sale.*', 'backend.admin.inventory.*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.sale.*', 'backend.admin.inventory.*']) ? 'active' : '' }}">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <p>
                        Reports
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('reports_summary')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.sale.summery')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.sale.summery']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sales Summary</p>
                        </a>
                    </li>
                    @endcan
                    @can('reports_sales')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.sale.report')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.sale.report']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sales</p>
                        </a>
                    </li>
                    @endcan
                    @can('reports_inventory')
                    <li class="nav-item">
                        <a href="{{route('backend.admin.inventory.report')}}"
                            class="nav-link {{ request()->routeIs(['backend.admin.inventory.report']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Inventory</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            {{-- ── SETTINGS Section Header ──────────────────────── --}}
            <li class="nav-header">SETTINGS</li>

            {{-- ── Website Settings ────────────────────────────── --}}
            <li class="nav-item {{ request()->routeIs(['backend.admin.settings.*', 'backend.admin.users*', 'backend.admin.roles*', 'backend.admin.permissions*', 'backend.admin.currencies*']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.settings.*', 'backend.admin.users*', 'backend.admin.roles*', 'backend.admin.permissions*', 'backend.admin.currencies*']) ? 'active' : '' }}">
                    <i class="fas fa-cog nav-icon"></i>
                    <p>
                        Website Settings
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @if (auth()->user()->hasAnyPermission(['website_settings']))
                    <li class="nav-item">
                        <a href="{{ route('backend.admin.settings.website.general') }}?active-tab=website-info"
                            class="nav-link {{ $route === 'backend.admin.settings.website.general' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>General Settings</p>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->hasAnyPermission(['currency_view', 'website_settings']))
                    <li class="nav-item">
                        <a href="{{ route('backend.admin.currencies.index') }}"
                            class="nav-link {{ request()->routeIs(['backend.admin.currencies.*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Currency</p>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->hasAnyPermission(['role_view', 'website_settings']))
                    <li class="nav-item {{ request()->routeIs(['backend.admin.roles*', 'backend.admin.permissions*']) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs(['backend.admin.roles*', 'backend.admin.permissions*']) ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                Roles & Permissions
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('role_view')
                            <li class="nav-item">
                                <a href="{{ route('backend.admin.roles') }}"
                                    class="nav-link {{ request()->routeIs(['backend.admin.roles*']) ? 'active' : '' }}">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('backend.admin.permissions') }}"
                                    class="nav-link {{ request()->routeIs(['backend.admin.permissions*']) ? 'active' : '' }}">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if (auth()->user()->hasAnyPermission(['user_view']))
                    <li class="nav-item">
                        <a href="{{ route('backend.admin.users') }}"
                            class="nav-link {{ $route === 'backend.admin.users' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>User Management</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>

<script>
    // Get all elements with the nav-treeview class
    const treeviewElements = document.querySelectorAll('.nav-treeview');

    // Iterate over each treeview element
    treeviewElements.forEach(treeviewElement => {
        // Check if it has the nav-link and active classes
        const navLinkElements = treeviewElement.querySelectorAll('.nav-link.active');

        // If there are nav-link elements with the active class, log the treeview element
        if (navLinkElements.length > 0) {
            // Add the menu-open class to the parent nav-item
            const parentNavItem = treeviewElement.closest('.nav-item');
            if (parentNavItem) {
                parentNavItem.classList.add('menu-open');
            }

            // Add the active class to the immediate child nav-link
            const childNavLink = parentNavItem.querySelector(':scope > .nav-link');
            if (childNavLink) {
                childNavLink.classList.add('active');
            }
        }
    });
</script>