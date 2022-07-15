<!-- Rename Modal -->
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
                    <input type="text" class="form-control" id="newFileName{{ $file->id }}" name="fileName" value={{
                        Str::beforeLast($file->fileName, '.'.pathinfo(storage_path($file->filePath),
                    PATHINFO_EXTENSION))
                    }}>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Rename</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    var modal{{ $file->id }} = document.getElementById('renameModal{{ $file->id }}')
    modal{{ $file->id }}.addEventListener('shown.bs.modal', function (event) {
        const input = document.getElementById('newFileName{{ $file->id }}');
        input.select();
    })
</script>