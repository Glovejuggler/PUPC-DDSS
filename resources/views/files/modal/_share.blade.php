<!-- Share Modal -->
<div class="modal fade" id="shareModal{{ $file->id }}" id="staticBackdrop" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('share.sharefile', $file->id) }}" method="post">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">{{ $file->fileName }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @forelse ($share_roles as $role)
                    @if ($role->id != Auth::user()->role_id)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="role_id[]"
                            id="checkbox{{ $file->id.'-'.$role->id }}" @foreach ($shares as $share) {{ $share->role_id
                        ==
                        $role->id && $share->file_id == $file->id ? 'checked' : '' }}
                        @endforeach>
                        <label class="form-check-label" for="checkbox{{ $file->id.'-'.$role->id }}">
                            {{ $role->roleName }}
                        </label>
                    </div>
                    @endif
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