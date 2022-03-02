@extends('layout.main')
@section('content')
<style>
    .center{
         width: 150px;
         margin: 40px auto;
     }

 </style>
<section>
	<h4 class="text-center">{{trans('file.My Order')}}</h4>
    @php
    //   dd($data_product[0]['real_qty'])
    @endphp
	<div class="table-responsive mt-3 m-4">
		<table class="table" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nama Kategori</th>
                    <th>Qty</th>
                    <th>Desc</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @for ($i=0; $i<count($data_product); $i++)
                    <tr>
                        <td>{{$data_product[$i][0]->code}}</td>
                        <td>{{$data_product[$i][0]->namaKategori}}</td>
                        <td>{{$data_product[$i]['real_qty']}}</td>
                        <td>{{$data_product[$i][0]->product_details}}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans("file.action")}}
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button class="btn btn-link view" onclick='openView(<?php echo json_encode($data_product[$i]);?>)'><i class="fa fa-eye"></i> {{trans('file.update')}}</button>
                                </li>
                                <li>
                                    <button class="btn btn-link view"><i class="fa fa-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                            </div>


                        </td>
                    </tr>
                @endfor
            </tbody>
		</table>
	</div>
</section>


<div id="product-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('update')}}</h5>
          <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12" id="product-content"></div>

                <div class="col-md-12">
                   <form action="{{route('products.editOrderUser')}}" method="post">
                        @csrf
                        <div class="center">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-danger btn-number" disabled="disabled" data-type="minus" data-field="quant">
                                        <span class="glyphicon glyphicon-minus">-</span>
                                    </button>
                                </span>
                                <input type="text" name="quant" id="inputNumber" class="form-control input-number" value="1" min="1" max="30">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-info btn-number" data-type="plus" data-field="quant">
                                        <span class="glyphicon glyphicon-plus">+</span>
                                    </button>
                                </span>
                            </div>

                        </div>
                        <input type="hidden" name="id_product" id="id_product">
                        <input type="hidden" name="id_user_order" id="id_user_order">
                        <input type="hidden" name="id_user" value="{{ Auth::id()}}">
                        <button type="submit" class="btn btn-block btn-info">Update</button>
                   </form>
                </div>
            </div>

            <h5 id="combo-header"></h5>
            <table class="table table-bordered table-hover item-list">
                <thead>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
      </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function openView(data){
        console.log(data);
        $('#id_product').val(data[0]['id']);
        $('#inputNumber').val(data['real_qty']);
        $("#inputNumber").attr('max',data[0]['qty']);
        $("#id_user_order").val(data['id_user_order']);
        $('#product-details').modal('show');
    }


    //input number
$(document).ready(function(){



    $('.btn-number').click(function(e){
        e.preventDefault();

        fieldName = $(this).attr('data-field');
        type      = $(this).attr('data-type');
        var input = $("input[name='"+fieldName+"']");
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if(type == 'minus') {

                if(currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                }
                if(parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if(type == 'plus') {

                if(currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
                if(parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });
    $('.input-number').focusin(function(){
        $(this).data('oldValue', $(this).val());
    });
    $('.input-number').change(function() {

        minValue =  parseInt($(this).attr('min'));
        maxValue =  parseInt($(this).attr('max'));
        valueCurrent = parseInt($(this).val());

        name = $(this).attr('name');
        if(valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        if(valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).val($(this).data('oldValue'));
        }


    });
    $(".input-number").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
});
</script>
@endpush
