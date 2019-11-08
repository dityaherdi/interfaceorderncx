@extends('layouts.master')

@section('css')
    
@endsection

@section('content')
<h4>Cek Order</h4>
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="customerTab" data-toggle="tab" href="#customerSection" role="tab" aria-controls="home" aria-selected="true">Nomor Customer</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="accountTab" data-toggle="tab" href="#accountSection" role="tab" aria-controls="profile" aria-selected="false">Nomor Account</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="pelayananTab" data-toggle="tab" href="#pelayananSection" role="tab" aria-controls="contact" aria-selected="false">Nomor Pelayanan</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="customerSection" role="tabpanel" aria-labelledby="customerTab">
      <form action="{{ route('order:byCustRef') }}" method="POST" class="mt-3">
        @csrf
        <div class="input-group">
          <input type="text" name="custRef" class="form-control" id="searchForm" placeholder="Nomor Customer">
          <input type="hidden" name="refType" value="nCust">
            <div class="input-group-prepend">
              <button type="submit" class="btn btn-danger btn-sm ml-2">
                <i class="material-icons">search</i> Find Order
              </button>
            </div>
        </div>
      </form>
    </div>
    <div class="tab-pane fade" id="accountSection" role="tabpanel" aria-labelledby="accountTab">
      <form action="{{ route('order:byCustRef') }}" method="POST" class="mt-3">
        @csrf
        <div class="input-group">
          <input type="text" name="custRef" class="form-control" id="searchForm" placeholder="Nomor Account">
          <input type="hidden" name="refType" value="nAcc">
            <div class="input-group-prepend">
              <button type="submit" class="btn btn-danger btn-sm ml-2">
                  <i class="material-icons">search</i> Find Order
                </button>
            </div>
        </div>
      </form>
    </div>
    <div class="tab-pane fade" id="pelayananSection" role="tabpanel" aria-labelledby="pelayananTab">
      <form action="{{ route('order:byCustRef') }}" method="POST" class="mt-3">
        @csrf
        <div class="input-group">
          <input type="text" name="custRef" class="form-control" id="searchForm" placeholder="Nomor Pelayanan">
          <input type="hidden" name="refType" value="nPel">
            <div class="input-group-prepend">
              <button type="submit" class="btn btn-danger btn-sm ml-2">
                  <i class="material-icons">search</i> Find Order
                </button>
            </div>
        </div>
      </form>
    </div>
  </div>
<hr>
<div class="d-flex justify-content-between align-items-center">
  @isset($inputValues)
    <h4>Results : '{{ $inputValues }}'</h4>
  @endisset
</div>
<div class="table-responsive">
    <table id="orderListTable" class="table table-striped table-bordered table-sm display nowrap" style="font-size: 12px;">
        <thead class="thead-dark">
          <tr>
            <th>{{ TypeHelper::uppercase('customer_ref') }}</th>
            <th>{{ TypeHelper::uppercase('account_num') }}</th>
            <th>{{ TypeHelper::uppercase('product_seq') }}</th>
            <th>{{ TypeHelper::uppercase('parent_product_seq') }}</th>
            <th>{{ TypeHelper::uppercase('product_id') }}</th>
            <th>{{ TypeHelper::uppercase('product_name') }}</th>
            <th>{{ TypeHelper::uppercase('cust_order_num') }}</th>
            <th>{{ TypeHelper::uppercase('supplier_order_num') }}</th>
            <th>{{ TypeHelper::uppercase('product_status') }}</th>
            <th>{{ TypeHelper::uppercase('status_reason_txt') }}</th>
            <th>{{ TypeHelper::uppercase('status_dtm') }}</th>
            <th>{{ TypeHelper::uppercase('product_label') }}</th>
            <th>{{ TypeHelper::uppercase('cps_name') }}</th>
            <th>{{ TypeHelper::uppercase('attribute_value') }}</th>
            <th>{{ TypeHelper::uppercase('attribute_subid') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_id') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_start') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_end') }}</th>
            <th>{{ TypeHelper::uppercase('product_quantity') }}</th>
            <th>{{ TypeHelper::uppercase('additions_quantity') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_name') }}</th>
            <th>{{ TypeHelper::uppercase('nrc') }}</th>
            <th>{{ TypeHelper::uppercase('mrc') }}</th>
            <th>{{ TypeHelper::uppercase('event_source') }}</th>
            <th>{{ TypeHelper::uppercase('event_source_txt') }}</th>
            <th>{{ TypeHelper::uppercase('rating_tariff') }}</th>
            <th>{{ TypeHelper::uppercase('event_type') }}</th>
          </tr>
        </thead>
        <tbody>
          @php
            if (!isset($res)) {
              $res = [];
            }
          @endphp
          @foreach ($res as $r)
            <tr>
              <td>{{ $r->customer_ref }}</td>
              <td>{{ $r->account_num }}</td>
              <td>{{ $r->product_seq }}</td>
              <td>{{ $r->parent_product_seq }}</td>
              <td>{{ $r->product_id }}</td>
              <td>{{ $r->product_name }}</td>
              <td>{{ $r->cust_order_num }}</td>
              <td>{{ $r->supplier_order_num }}</td>
              <td>{{ $r->product_status }}</td>
              <td>{{ $r->status_reason_txt }}</td>
              <td>{{ $r->status_dtm }}</td>
              <td>{{ $r->product_label }}</td>
              <td>{{ $r->cps_name }}</td>
              <td>{{ $r->attribute_value }}</td>
              <td>{{ $r->attribute_subid }}</td>
              {{-- <td>{{ $r->address }}</td> --}}
              <td>{{ $r->tariff_id }}</td>
              <td>{{ $r->tariff_start }}</td>
              <td>{{ $r->tariff_end }}</td>
              <td>{{ $r->product_quantity }}</td>
              <td>{{ $r->additions_quantity }}</td>
              <td>{{ $r->tariff_name }}</td>
              <td>{{ $r->nrc }}</td>
              <td>{{ $r->mrc }}</td>
              <td>{{ $r->event_source }}</td>
              <td>{{ $r->event_source_txt }}</td>
              <td>{{ $r->rating_tariff }}</td>
              <td>{{ $r->event_type }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="thead-dark">
          <tr>
            <th>{{ TypeHelper::uppercase('customer_ref') }}</th>
            <th>{{ TypeHelper::uppercase('account_num') }}</th>
            <th>{{ TypeHelper::uppercase('product_seq') }}</th>
            <th>{{ TypeHelper::uppercase('parent_product_seq') }}</th>
            <th>{{ TypeHelper::uppercase('product_id') }}</th>
            <th>{{ TypeHelper::uppercase('product_name') }}</th>
            <th>{{ TypeHelper::uppercase('cust_order_num') }}</th>
            <th>{{ TypeHelper::uppercase('supplier_order_num') }}</th>
            <th>{{ TypeHelper::uppercase('product_status') }}</th>
            <th>{{ TypeHelper::uppercase('status_reason_txt') }}</th>
            <th>{{ TypeHelper::uppercase('status_dtm') }}</th>
            <th>{{ TypeHelper::uppercase('product_label') }}</th>
            <th>{{ TypeHelper::uppercase('cps_name') }}</th>
            <th>{{ TypeHelper::uppercase('attribute_value') }}</th>
            <th>{{ TypeHelper::uppercase('attribute_subid') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_id') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_start') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_end') }}</th>
            <th>{{ TypeHelper::uppercase('product_quantity') }}</th>
            <th>{{ TypeHelper::uppercase('additions_quantity') }}</th>
            <th>{{ TypeHelper::uppercase('tariff_name') }}</th>
            <th>{{ TypeHelper::uppercase('nrc') }}</th>
            <th>{{ TypeHelper::uppercase('mrc') }}</th>
            <th>{{ TypeHelper::uppercase('event_source') }}</th>
            <th>{{ TypeHelper::uppercase('event_source_txt') }}</th>
            <th>{{ TypeHelper::uppercase('rating_tariff') }}</th>
            <th>{{ TypeHelper::uppercase('event_type') }}</th>
          </tr>
        </tfoot>
    </table>
  </div>
  <hr>
@endsection

@push('js')
  <script>
    $(document).ready( function () {
      $('#orderListTable').DataTable({
        scrollY: "700px",
        scrollX: true,
        responsive: true,
        // fixedHeader: true,
        fixedHeader: {
            header: true,
            footer: true
        },
        scrollCollapse: true,
        columnDefs: [
            { width: '100%', targets: 0 }
        ],
      });
    });
  </script>
@endpush