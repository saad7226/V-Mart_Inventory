@extends('backend.master')

@section('title', 'Product Purchase')

@section('content')
<section class="content-header" id="purchase">
</section>
@endsection

@push('style')
<style>
  /* 1. FORCE THE WRAPPERS TO BE BLOCKS (NO FLEX) */
  .react-datepicker-wrapper,
  .react-datepicker__input-container,
  .date-picker-container {
    display: block !important;
    width: 100% !important;
  }

  /* 2. THE CALENDAR BOX ITSELF */
  .react-datepicker {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: absolute !important;
    z-index: 99999 !important;
    background-color: #fff !important;
    border: 1px solid #ccc !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
    width: 280px !important;
    height: auto !important;
    padding: 10px !important;
  }

  /* 3. THE MONTH CONTAINER - NO FLEX */
  .react-datepicker__month-container {
    display: block !important;
    float: none !important;
    width: 100% !important;
    background: #fff !important;
  }

  /* 4. THE MONTH - NO FLEX */
  .react-datepicker__month {
    display: block !important;
    margin: 5px !important;
    padding: 0 !important;
  }

  /* 5. THE WEEK - MUST BE BLOCK (THIS FIXES THE ONE-ROW ISSUE) */
  .react-datepicker__week {
    display: block !important;
    clear: both !important;
    white-space: nowrap !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  /* 6. THE DAYS - MUST BE INLINE-BLOCK */
  .react-datepicker__day,
  .react-datepicker__day-name {
    display: inline-block !important;
    float: none !important;
    width: 32px !important;
    height: 32px !important;
    line-height: 32px !important;
    text-align: center !important;
    margin: 2px !important;
    border-radius: 4px !important;
  }

  /* 7. HEADER FIX */
  .react-datepicker__header {
    display: block !important;
    width: 100% !important;
    background-color: #fff !important;
    border-bottom: 1px solid #eee !important;
    text-align: center !important;
    padding: 5px 0 !important;
  }

  .react-datepicker__current-month {
    display: block !important;
    font-weight: bold !important;
    font-size: 1rem !important;
  }
</style>
@endpush
@push('script')
<script>
</script>
@endpush