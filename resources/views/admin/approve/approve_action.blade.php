@if($status=="pending")
    <div class="form-button-action">
        <button data-toggle="tooltip" id="editBtn" class="btn btn-sm btn-icon btn-info" data-original-title="Edit" data-id="{{ $id }}"><i class="fa fa-edit"></i>
        </button>
        <button data-toggle="tooltip" id="deleteBtn" class="btn btn-sm btn-icon btn-danger" data-original-title="Delete" data-id="{{ $id }}">
            <i class="fa fa-trash-alt"></i>
        </button>
    </div>
@else

@endif



