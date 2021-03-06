<table class="hover compact" id="fileIndexTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Uploader</th>
            <th>Date deleted</th>
            <th>Date uploaded</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($folders as $folder)
        <tr>
            <td><i class="fas fa-folder text-yellow"></i> {{ $folder->folderName }}</td>
            <td>{{ $folder->user->full_name() }}</td>
            <td>{{ $folder->deleted_at->format('M j, Y \a\t g:i:s A') }}</td>
            <td>{{ $folder->created_at->format('M j, Y \a\t g:i:s A') }}</td>
            <td>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('folder.recover', $folder->id) }}" class="btn btn-sm btn-success">Restore</a>
                </div>
            </td>
        </tr>
        @empty

        @endforelse
        @foreach ($files as $file)
        <tr>
            <td>{{ $file->fileName }}</td>
            <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->first_name.'
                '.$file->user->last_name
                }}</td>
            <td>{{ $file->deleted_at->format('M j, Y \a\t g:i:s A') }}</td>
            <td>{{ $file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
            <td>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('file.recover', $file->id) }}" class="btn btn-sm btn-success mr-1">Restore</a>
                    <a href="{{ route('file.download', $file->id) }}" download class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>