<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Import số LinkGo</h3>

                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 10px">
                        <a href="{{ route('listphones.index') }}" class="btn btn-sm btn-default"><i
                                    class="fa fa-list"></i>&nbsp;Danh sách</a>
                    </div>

                </div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{ route('saveImport') }}" method="POST" accept-charset="UTF-8"
                  class="form-horizontal" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group  ">
                        <label for="import" class="col-sm-2 control-label">Import Files</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file fa-fw"></i></span>
                                <input type="file" name="file" class="form-control ">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {{ csrf_field() }}
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8">
                        <div class="btn-group pull-right">
                            <button type="submit" class="btn btn-info pull-right"
                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Gửi">Gửi
                            </button>
                        </div>

                        <div class="btn-group pull-left">
                            <button type="reset" class="btn btn-warning">Cài lại</button>
                        </div>

                    </div>

                </div>
                <!-- /.box-footer -->
            </form>
        </div>

    </div>
</div>
