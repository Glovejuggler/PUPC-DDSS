<!-- Share Modal -->
<div class="modal fade" id="shareFolderModal{{ $folder->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="shareFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('folder.share', $folder->id) }}" method="post">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="shareFolderModalLabel">{{ $folder->folderName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @forelse ($share_roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="role_id[]"
                            id="checkbox{{ $folder->id.'-'.$role->id }}" @foreach ($shares as $share) {{ $share->role_id
                        ==
                        $role->id && $share->folder_id == $folder->id ? 'checked' : '' }}
                        @endforeach>
                        <label class="form-check-label" for="checkbox{{ $folder->id.'-'.$role->id }}">
                            {{ $role->roleName }}
                        </label>
                    </div>
                    @empty
                    <p>No other roles to share this file to.</p>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary">Share</button>
                </div>
            </form>
        </div>
    </div>
</div>