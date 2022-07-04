<!-- Modal -->
<div class="modal fade" id="renameModal{{ $file->id }}" tabindex="-1" aria-labelledby="renameModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameModalLabel">{{ $file->fileName }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('file.rename', $file->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <label for="newFileName" class="form-label">Rename to:</label>
                    <input type="text" class="form-control" id="newFileName" name="fileName">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>