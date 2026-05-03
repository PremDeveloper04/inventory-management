<!DOCTYPE html>
<html>
<head>
    <title>Workers</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .card { border-radius: 10px; }
        .table th { white-space: nowrap; }
    </style>
</head>
<body>

<div class="container mt-4">

    <h3 class="mb-4">Workers List</h3>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- FILTER PANEL --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('workers.index') }}">

                <div class="row g-3">

                    <div class="col-md-2">
                        <input type="text" name="name" class="form-control"
                               placeholder="Name"
                               value="{{ request('name') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="text" name="email" class="form-control"
                               placeholder="Email"
                               value="{{ request('email') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Status</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="text" name="city" class="form-control"
                               placeholder="City"
                               value="{{ request('city') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Apply</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('workers.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>

                    {{-- OLD EXPORT (commented) --}}
                    {{-- 
                    <div class="col-md-2">
                        <button type="button"
                                onclick="exportData()"
                                class="btn btn-success w-100">
                            Export
                        </button>
                    </div>
                    --}}

                    {{-- NEW ASYNC EXPORT --}}
                    <div class="col-md-2">
                        <button type="button"
                                onclick="startExport()"
                                id="exportBtn"
                                class="btn btn-success w-100">
                            Export
                        </button>
                    </div>

                    <div class="col-md-2">
                        <button type="button"
                                id="downloadBtn"
                                class="btn btn-primary w-100"
                                disabled>
                            Download
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Sno.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>City</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($workers as $worker)
                            <tr>
                                <td>
                                    {{ ($workers->currentPage() - 1) * $workers->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ $worker->name }}</td>
                                <td>{{ $worker->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $worker->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $worker->status }}
                                    </span>
                                </td>
                                <td>{{ $worker->city }}</td>
                                <td>{{ $worker->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $workers->links() }}
            </div>

        </div>
    </div>

</div>

</body>


<script>
/*function exportData() {
    let params = new URLSearchParams(new FormData(document.querySelector('form')));
    window.location.href = "{{ route('workers.export') }}?" + params.toString();
}*/

let exportId = null;
let interval = null;

function startExport() {
    let form = document.querySelector('form');
    let params = new URLSearchParams(new FormData(form));

    document.getElementById('exportBtn').innerText = 'Processing...';
    document.getElementById('exportBtn').disabled = true;

    fetch("{{ url('/start-export') }}?" + params.toString())
        .then(res => res.json())
        .then(data => {
            exportId = data.export_id;
            checkStatus();
        });
}

function checkStatus() {
    interval = setInterval(() => {
        fetch(`/check-export/${exportId}`)
            .then(res => res.json())
            .then(data => {

                if (data.status === 'processing') {
                    document.getElementById('downloadBtn').innerText = 'Please wait...';
                }

                if (data.status === 'completed') {
                    clearInterval(interval);

                    let btn = document.getElementById('downloadBtn');

                    btn.disabled = false;
                    btn.innerText = 'Download';

                    btn.onclick = function () {
                        window.location.href = `/download-export/${exportId}`;
                    };

                    document.getElementById('exportBtn').innerText = 'Export';
                    document.getElementById('exportBtn').disabled = false;
                }

                if (data.status === 'failed') {
                    clearInterval(interval);
                    alert('Export failed');
                    document.getElementById('exportBtn').innerText = 'Export';
                    document.getElementById('exportBtn').disabled = false;
                }
            });
    }, 3000);
}
</script>

</html>