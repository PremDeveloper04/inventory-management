<!DOCTYPE html>
<html>
<head>
    <title>Exports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">
        <h3>Export History</h3>

        <a href="{{ route('workers.index') }}"
           class="btn btn-primary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Export Name</th>
                        <th>Filters</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Processed</th>
                        <th>Total</th>
                        <th>Created At</th>
                        <th>Files</th>
                        <th>Download</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($exports as $export)

                    @php
                        $progress = 0;

                        if ($export->total_records > 0) {
                            $progress = round(
                                ($export->processed_records / $export->total_records) * 100
                            );
                        }
                    @endphp

                    <tr>

                        <td>{{ $export->id }}</td>

                        <td>{{ $export->export_name }}</td>

                        <td width="250">

                            @php
                                $filters = collect($export->filters)
                                    ->filter(function ($value) {
                                        return !empty($value);
                                    });
                            @endphp

                            @forelse($filters as $key => $value)

                                <span class="badge bg-secondary mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                    {{ $value }}
                                </span>

                            @empty

                                <span class="text-muted">No Filters</span>

                            @endforelse

                        </td>

                        

                        <td>
                            <span class="badge bg-{{
                                $export->status == 'completed'
                                    ? 'success'
                                    : ($export->status == 'failed'
                                        ? 'danger'
                                        : 'warning')
                            }}">
                                {{ ucfirst($export->status) }}
                            </span>
                        </td>

                        <td width="220">

                            <div class="progress">

                                <div class="progress-bar
                                    {{ $progress == 100 ? 'bg-success' : '' }}"
                                    role="progressbar"
                                    style="width: {{ $progress }}%">

                                    {{ $progress }}%
                                </div>

                            </div>

                        </td>

                        <td>
                            {{ $export->processed_records }}
                        </td>

                        <td>
                            {{ $export->total_records }}
                        </td>

                        <td>{{ $export->created_at }}</td>

                        <td>

                            @foreach($export->files as $file)

                                <a href="{{ asset('storage/'.$file->file_name) }}"
                                   class="btn btn-sm btn-success mb-1"
                                   download>

                                    Part {{ $file->part_number }}

                                </a>

                            @endforeach

                        </td>

                        <td>
                            @if($export->status == 'completed')

                                @foreach($export->files as $file)

                                    <a href="{{ asset('storage/'.$file->file_name) }}"
                                    class="btn btn-sm btn-success mb-1"
                                    download>

                                        Download Part {{ $file->part_number }}

                                    </a>

                                @endforeach

                            @else

                                <span class="text-muted">
                                    Generating...
                                </span>

                            @endif
                        </td>

                        <td>

                            <form method="POST"
                                  action="{{ route('exports.delete', $export->id) }}">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="12" class="text-center">
                            No exports found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

            {{ $exports->links() }}

        </div>
    </div>

</div>

</body>


<script>

const hasProcessing = @json(
    $exports->contains(fn($e) =>
        in_array($e->status, ['pending', 'processing'])
    )
);

if (hasProcessing) {

    setInterval(() => {
        window.location.reload();
    }, 5000);

}

</script>

</html>