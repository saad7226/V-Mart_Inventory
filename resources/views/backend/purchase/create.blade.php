@extends('backend.master')

@section('title', 'Product Purchase')

@section('content')
<section class="content-header" id="purchase">
</section>
@endsection

@push('style')
<style>
  .date-picker-container {
    width: 100% !important;
    position: relative;
  }
  .react-datepicker-wrapper {
    width: 100% !important;
    display: block !important;
  }
  .react-datepicker__input-container {
    width: 100% !important;
    display: block !important;
  }
  .react-datepicker__input-container input {
    width: 100% !important;
  }
  /* Force the calendar to show correctly */
  .react-datepicker {
    font-family: inherit !important;
    display: inline-block !important;
    width: auto !important;
    background-color: #fff !important;
    border: 1px solid #ddd !important;
    border-radius: 8px !important;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
  }
  .react-datepicker__month-container {
    float: none !important;
    width: 300px !important;
    background: white !important;
  }
  @media (max-width: 480px) {
    .react-datepicker__month-container {
      width: 260px !important;
    }
  }
</style>
@endpush
@push('script')
<script>
</script>
@endpush