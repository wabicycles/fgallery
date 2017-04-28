@extends('admin/master/index')
@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                        <table class="table table-bordered table-striped table-hover" id="images-table">
                            <thead>
                            <tr>
                                <th>User Id</th>
                                <th>Avatar</th>
                                <th>Username</th>
                                <th>FullName</th>
                                <th>Email</th>
                                <th>Images</th>
                                <th>Comments</th>
                                <th>Featured At</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            <div class="row">
                    <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers"></div>
                    </div>
                </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(function () {
            @if(env('APP_DEBUG') == false)$.fn.dataTable.ext.errMode = 'none';@endif
            var table = $('#images-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: '{!! route('admin.users.data', ['type' => $type]) !!}',
                columns: [
                    {data: 'id', name: 'users.id'},
                    {data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false},
                    {data: 'username', name: 'username',},
                    {data: 'fullname', name: 'fullname',},
                    {data: 'email', name: 'users.email',},
                    {data: 'images', name: 'images', searchable: false},
                    {data: 'comments', name: 'comments', searchable: false},
                    {data: 'featured_at', name: 'users.featured_at'},
                    {data: 'created_at', name: 'users.created_at'},
                    {data: 'updated_at', name: 'users.updated_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                "fnDrawCallback": function () {
                    userApprove();
                    userDisapprove();
                }
            });
            table.on('click', '.sorting_1', function () {
                userApprove();
                userDisapprove();
            });
        });
    </script>
@endsection