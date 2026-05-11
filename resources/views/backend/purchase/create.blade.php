@extends('backend.master')

@section('title', 'Product Purchase')

@section('content')
<section class="content-header" id="purchase">
</section>
@endsection

@push('style')
<style>
  .react-datepicker-wrapper {
    width: 100%;
    display: block !important;
  }
  .react-datepicker__input-container {
    width: 100%;
    display: block !important;
  }
  .react-datepicker {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    border-radius: 15px !important;
    border: none !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    overflow: hidden;
    width: 300px !important;
  }
  .react-datepicker__header {
    background-color: #ffffff !important;
    border-bottom: 1px solid #f0f0f0 !important;
    padding-top: 15px !important;
  }
  .react-datepicker__day--selected {
    background-color: #FF473D !important;
    border-radius: 10px !important;
  }
  @media (max-width: 768px) {
    .react-datepicker {
      width: 250px !important;
    }
  }
</style>
@endpush
@push('script')
<script>
</script>
@endpush