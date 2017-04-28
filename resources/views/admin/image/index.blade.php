@extends('admin/master/index')
@section('content')
    <div class="box">
        <div class="box-body">
            <div class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                        <table class="table table-bordered table-hover table-striped" id="images-table">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Thumbnail</th>
                                <th>Title</th>
                                <th>Uploaded By</th>
                                <th>Favorites</th>
                                <th>Downloads</th>
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
    </div>
@endsection

@section('extra-js')
    <script>
        $(function () {
            var table = $('#images-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: '{!! route('admin.images.data', ['type' => $type]) !!}',
                columns: [
                    {data: 'id', name: 'images.id'},
                    {data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false},
                    {data: 'title', name: 'images.title'},
                    {data: 'user.username', name: 'user.username'},
                    {data: 'favorites', name: 'favorites', orderable: false, searchable: false},
                    {data: 'downloads', name: 'images.downloads'},
                    {data: 'featured_at', name: 'images.featured_at'},
                    {data: 'created_at', name: 'images.created_at'},
                    {data: 'updated_at', name: 'images.updated_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "fnDrawCallback": function () {
                    imageApprove();
                    imageDisapprove();
                }
            });
            table.on('click', '.sorting_1', function () {
                imageApprove();
                imageDisapprove();
            });
        });
    </script>
@endsection