@extends('admin/master/index')

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-maroon-active"><i class="ion ion-images"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Images</span>
                    <span class="info-box-number">{{ \App\Artvenue\Models\Image::approved()->count() }}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-ios-chatboxes-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Comments</span>
                    <span class="info-box-number">{{ \App\Artvenue\Models\Comment::count() }}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-ios-people"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Users</span>
                    <span class="info-box-number">{{ \App\Artvenue\Models\User::count() }}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="ion ion-android-star-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Featured Images</span>
                    <span class="info-box-number">{{ \App\Artvenue\Models\Image::approved()->whereNotNull('featured_at')->count() }}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
    </div>
    <div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daily Signup Chart</h3>
        </div>
        <div class="box-body chart-responsive">
            <div class="col-md-12 chart">
                <div id="signup-container"></div>
            </div>
        </div>
    </div>
        <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Daily Image Upload Chart</h3>
        </div>
        <div class="box-body chart-responsive">
            <div class="col-md-12 chart">
                <div id="images-container"></div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-lg-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Site Details ( Quick Look )</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <div id="donut-chart"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('extra-js')
    <script>
        Morris.Donut({
            element: 'donut-chart',
            resize: true,
            colors: ["#3c8dbc", "#f56954", "#00a65a"],
            data: [
                {label: "Images", value: {{ \App\Artvenue\Models\Image::count() }} },
                {label: "Users", value: {{ \App\Artvenue\Models\User::count() }}},
                {label: "Comments", value: {{ \App\Artvenue\Models\Comment::count() }} },
                {label: "Reply", value: {{ \App\Artvenue\Models\Reply::count() }} },
            ]
        });

        var data = JSON.parse('{!! $signup !!}');
        new Morris.Area({
            element: 'signup-container',
            data: data,
            xkey: 'date',
            ykeys: ['value'],
            labels: ['Users'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto',
            parseTime: false,
            resize: true
        });

        var newsdata = JSON.parse('{!! $images !!}');
        new Morris.Area({
            element: 'images-container',
            data: newsdata,
            xkey: 'date',
            ykeys: ['value'],
            labels: ['Images'],
            lineColors: ['#3c8dbc'],
            hideHover: 'auto',
            parseTime: false,
            resize: true
        });
    </script>
@endsection