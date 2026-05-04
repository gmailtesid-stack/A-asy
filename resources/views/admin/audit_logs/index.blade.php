@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-white mb-1"><i class="fas fa-shield-alt me-2 text-primary"></i>Audit Trails</h2>
            <p class="text-white-50">Log aktivitas sistem untuk keamanan dan akuntabilitas.</p>
        </div>
    </div>

    <div class="card bg-glass border-0 shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Model</th>
                            <th>Data Lama</th>
                            <th>Data Baru</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td class="small">{{ $log->created_at }}</td>
                            <td>
                                <div class="fw-bold">{{ $log->user->name ?? 'System' }}</div>
                                <div class="small text-white-50">{{ $log->user->email ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'warning' : 'danger') }}-soft">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ class_basename($log->model_type) }}</div>
                                <div class="small text-white-50">ID: {{ $log->model_id }}</div>
                            </td>
                            <td class="small">
                                @if($log->old_values)
                                <pre class="mb-0 text-white-50" style="font-size: 0.7rem;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                -
                                @endif
                            </td>
                            <td class="small">
                                @if($log->new_values)
                                <pre class="mb-0 text-white-50" style="font-size: 0.7rem;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                -
                                @endif
                            </td>
                            <td class="small text-white-50">{{ $log->ip_address }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

<style>
    .bg-glass {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .table-dark-custom { color: white; background: transparent; }
    .table-dark-custom thead th {
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        padding: 1.25rem 1rem;
    }
    .table-dark-custom td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
        vertical-align: top;
    }
    .bg-success-soft { background: rgba(25, 135, 84, 0.2); color: #198754; }
    .bg-warning-soft { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .bg-danger-soft { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
</style>
@endsection
