@extends('layouts.master')

@section('css')
    
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center">
  <h1>Table</h1>
  <a href="{{ route('db:test') }}" class="btn btn-danger btn-sm">Test Database Connection</a>
</div>
<div class="table-responsive mt-3">
    <table id="orderListTable" class="table table-striped table-sm">
        <thead>
          <tr>
              <th>Column 1</th>
              <th>Column 2</th>
              <th>Column 2</th>
          </tr>
        </thead>
        <tbody>
          @php
            if (!isset($test)) {
              $test = [];
            }    
          @endphp
          @foreach ($test as $t)
            <tr>
              <td>{{ $t->start_dat }}</td>
              <td>{{ $t->end_dat }}</td>
              <td>{{ $t->credit_limit_mny }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@push('js')
  <script>
    $(document).ready( function () {
      $('#orderListTable').DataTable();
    });
  </script>
@endpush