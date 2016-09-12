@extends('admin.app')

@section('title')
    Edit Admins
@endsection

@push('styles')
{!! Html::style('css/animate.css') !!}
@endpush

@section('content')
<div class="container">
    <div class="row">
        @include('admin.partials.sidebar')
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Edit Admin
                    <div class="pull-right">
                        <a href={{ route('admins.index') }} class="btn btn-primary admin-add-button">
                        <i class="fa fa-list" aria-hidden="true"></i> List
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                {!! Form::model($admin, 
                    ['url' => route('admins.update', $admin->hashid), 
                    'id' => 'admin-form', 
                    'method' => 'PATCH']) !!}
                    <div class="form-group">
                        <label for="name">Name</label>
                        {!! Form::text('name', 
                                        NULL, 
                                        ['class' => 'form-control', 
                                        'id' => 'name', 
                                        'placeholder' => 'Enter name']) !!}
                        {{ ($errors->has('name') ? $errors->first('name') : '') }}
                        <span class="form-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="form-control">
                            {{ $admin->email }}
                        </div>
                        <span class="form-error"></span>
                    </div>
                    <button class="btn btn-primary" id="add-button">Update</button>
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
{!! Html::script('js/jquery.noty.packaged.js') !!}
<script>
    
    $('form').submit(function(e){
        e.preventDefault();

        $('span.form-error').hide();
        $('#add-button').text('Updating...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serializeArray(),
            type: 'POST',
            dataType: 'json',
            success: function(res)
            {
                console.log(res);
                // $('#admin-form')[0].reset();
                generate('success', '<div class="activity-item">\
                    <i class="fa fa-check text-success"></i>\
                    <div class="activity">' + res.success + '</div></div>');
                },
            error: function(res)
            {
                var errors = JSON.parse(res.responseText);
                $.each(errors, function(key, value){
                    $('#'+key).siblings('span.form-error').html(value).fadeIn();
                });
            },
            complete: function()
            {
                $('#add-button').text('Update').prop('disabled', false);
            }
        });
    });

    function generate(type, text) {

        var n = noty({
            text        : text,
            type        : type,
            layout      : 'topRight',
            theme       : 'relax',
            dismissQueue: true,
            timeout     : 4000,
            closeWith   : ['click', 'timeout'],
            animation   : {
                open  : 'animated fadeInDown',
                close : 'animated fadeOutUp',
                easing: 'swing',
                speed : 500
            },
            buttons: false
        });
    }
</script>
@endpush
