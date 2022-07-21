<!-- Rename Modal -->
<div class="modal fade" id="renameFolderModal{{ $folder->id }}" tabindex="-1" aria-labelledby="renameFolderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameFolderModalLabel">{{ $folder->folderName }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('folder.rename', $folder->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <label for="newFileName" class="form-label">Rename to:</label>
                    <input type="text" class="form-control" id="newFolderName{{ $folder->id }}" name="folderName"
                        value={{ $folder->folderName }}>
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
    var modal{{ $folder->id }} = document.getElementById('renameFolderModal{{ $folder->id }}')
    modal{{ $folder->id }}.addEventListener('shown.bs.modal', function (event) {
        const input = document.getElementById('newFolderName{{ $folder->id }}');
        input.select();
    })
</script>