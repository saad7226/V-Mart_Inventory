@extends('backend.master')
@section('title', 'Pos')
@section('content')
<div class="pos-wrapper p-3">
    <div id="cart" class="pos-container"></div>
</div>
@push('style')
<style>
    .pos-wrapper {
        background: #f8fafc;
        min-height: calc(100vh - 100px);
        border-radius: 30px;
    }

    .pos-container {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 20px;
        min-height: 80vh;
    }

    .products-card-container {
        max-height: 60vh;
        overflow-y: auto;
        overflow-x: hidden;
        border: none;
        padding: 15px;
        background: #fdfdfd;
        border-radius: 18px;
    }

    /* Custom Slim Scrollbar for POS */
    .products-card-container::-webkit-scrollbar { width: 4px; }
    .products-card-container::-webkit-scrollbar-track { background: transparent; }
    .products-card-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .products-card-container::-webkit-scrollbar-thumb:hover { background: var(--primary); }

    .product-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 5px;
    }

    .product-details p {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .loading-more {
        text-align: center;
        /* Center the loading message */
        padding: 10px;
        /* Add some padding */
        font-weight: bold;
        /* Make the text bold */
    }

    .responsive-table {
        height: 100%;
        overflow-y: scroll;

    }

    .qty {
        /* Hides the default number input spinner */
        -moz-appearance: textfield;
        /* Firefox */
        -webkit-appearance: none;
        /* Chrome/Safari */
        appearance: none;
        /* Standard */
    }

    .qty::-webkit-inner-spin-button,
    .qty::-webkit-outer-spin-button {
        display: none;
        /* Hides the spin buttons */
    }
    @media (max-width: 768px) {
        .pos-wrapper { padding: 10px; border-radius: 15px; }
        .pos-container { padding: 15px; border-radius: 15px; }
        .products-card-container { max-height: 40vh; }
        .product-name { font-size: 13px; }
        .product-details p { font-size: 11px; }
    }
</style>
@endpush
@endsection