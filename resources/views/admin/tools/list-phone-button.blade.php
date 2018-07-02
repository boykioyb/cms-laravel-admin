<div class="btn-group pull-right" data-toggle="buttons">

    <a href="./listphones/create" class="btn btn-sm btn-success" style="margin-right: 10px">@lang('translate.create_linkgo_number')</a>
    <a href="javascript:void(0)" class="btn btn-sm btn-danger grid-batch-1" style="margin-right: 10px">@lang('translate.evict_linkgo_number')</a>
    <div class="btn-group pull-right" style="margin-right: 10px">
        <a class="btn btn-sm  btn-twitter"><i class="fa fa-file-excel-o"></i> Import</a>
        <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ route('formImport') }}">@lang('translate.import_linkgo_number')</a></li>
            <li><a href="{{ route('formImportEvict') }}">@lang('translate.import_evict_linkgo_number')</a></li>
        </ul>
    </div>
</div>