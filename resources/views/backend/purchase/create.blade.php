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
  /* FORCE CALENDAR STRUCTURE - PREVENT FLEX SQUASHING */
  .react-datepicker {
    display: block !important;
    width: 260px !important;
    visibility: visible !important;
    opacity: 1 !important;
    background: #fff !important;
    border: 1px solid #aeaeae !important;
    border-radius: 0.3rem !important;
  }
  .react-datepicker__month-container {
    display: block !important;
    float: none !important;
    width: 100% !important;
  }
  .react-datepicker__month {
    display: block !important;
    margin: 0.4rem !important;
    text-align: center !important;
  }
  .react-datepicker__week {
    display: block !important;
    white-space: nowrap !important;
  }
  .react-datepicker__day, 
  .react-datepicker__day-name {
    display: inline-block !important;
    width: 1.7rem !important;
    line-height: 1.7rem !important;
    margin: 0.166rem !important;
    text-align: center !important;
  }
  .react-datepicker__header {
    display: block !important;
    text-align: center !important;
    background-color: #f0f0f0 !important;
    border-bottom: 1px solid #aeaeae !important;
    border-top-left-radius: 0.3rem !important;
    padding-top: 8px !important;
  }
  .react-datepicker__current-month {
    display: block !important;
  }
</style>
@endpush
@push('script')
<script>
</script>
@endpush