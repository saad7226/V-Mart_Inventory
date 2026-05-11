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
    width: auto !important; /* Let it be auto or set a reasonable min-width */
    min-width: 300px !important;
    visibility: visible !important;
    opacity: 1 !important;
    background: #fff !important;
    border: 1px solid #ddd !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    z-index: 9999 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
  }
  .react-datepicker__month-container {
    display: block !important;
    float: none !important;
    width: 100% !important;
    padding: 10px !important;
  }
  .react-datepicker__month {
    display: block !important;
    margin: 0.4rem !important;
    text-align: center !important;
  }
  .react-datepicker__week {
    display: flex !important;
    justify-content: space-around !important;
    white-space: nowrap !important;
  }
  .react-datepicker__day, 
  .react-datepicker__day-name {
    display: inline-block !important;
    width: 2.5rem !important;
    line-height: 2.5rem !important;
    margin: 0.1rem !important;
    text-align: center !important;
    border-radius: 50% !important;
  }
  .react-datepicker__day:hover {
    background-color: #f3f4f6 !important;
  }
  .react-datepicker__day--selected {
    background-color: #4F46E5 !important;
    color: white !important;
  }
  .react-datepicker__header {
    display: block !important;
    text-align: center !important;
    background-color: #fff !important;
    border-bottom: 1px solid #eee !important;
    border-top-left-radius: 12px !important;
    border-top-right-radius: 12px !important;
    padding-top: 12px !important;
  }
  .react-datepicker__current-month {
    display: block !important;
    font-weight: 700 !important;
    font-size: 1.1rem !important;
    margin-bottom: 10px !important;
  }
  .react-datepicker__navigation {
    top: 15px !important;
  }
  .react-datepicker__navigation--previous {
    left: 10px !important;
  }
  .react-datepicker__navigation--next {
    right: 10px !important;
  }
  /* Portal z-index */
  #datepicker-portal {
    z-index: 9999;
  }
</style>
@endpush
@push('script')
<script>
</script>
@endpush