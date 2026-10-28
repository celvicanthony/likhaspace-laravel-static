@extends('layouts.app')

@section('title', 'Manage Users | LikhaSpace')

@push('styles')
<style>
    .users-page { color: var(--studio-text); }
    .users-page .table { --bs-table-bg: transparent; --bs-table-color: var(--studio-text); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--studio-text); margin-bottom: 0; }
    .users-page .table th { background: var(--surface-soft); color: var(--studio-muted); font-size: .72rem; white-space: nowrap; }
    .users-page .table td, .users-page .table th { border-color: var(--studio-border); vertical-align: middle; }
    .users-page .form-control, .users-page .form-select { border-color: var(--studio-border); background-color: var(--surface-soft); color: var(--studio-text); }
    .user-avatar { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border-radius: 50%; background: var(--surface-strong); color: var(--studio-link); font-weight: 700; }
    .role-select { min-width: 118px; }
    .user-status { border: 1px solid var(--studio-border); background: var(--surface-strong); color: var(--studio-link); }
    .user-status[data-status="Suspended"] { background: var(--surface-soft); color: var(--studio-muted); }
    .user-action-form { display: inline-flex; }
    @media (max-width: 575.98px) { .user-actions { min-width: 160px; } }
</style>
@endpush

@section('content')
<div class="users-page">
    <header class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <div class="home-kicker">Platform administration</div>
            <h1 class="h3 fw-bold mb-1">Manage users</h1>
            <p class="text-muted mb-0">Manage client and artist accounts. The platform has one protected administrator account.</p>
        </div>
        <span class="badge rounded-pill px-3 py-2" style="background: var(--studio-primary); color: var(--studio-primary-text)"><i class="bi bi-people me-1"></i>{{ count($users) }} accounts</span>
    </header>

    @if (session('status'))<div class="alert bg-studio-card rounded-4" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert bg-studio-card rounded-4" role="alert">{{ $errors->first() }}</div>@endif

    <section class="bg-studio-card rounded-4 p-3 p-md-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-3">
            <div><h2 class="h6 fw-bold mb-1">Marketplace accounts</h2><p class="text-muted small mb-0">Search by name or email to manage access.</p></div>
            <label class="visually-hidden" for="user-search">Search users</label>
            <input class="form-control" style="max-width: 360px" id="user-search" type="search" placeholder="Search name or email">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th scope="col">User</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Joined</th><th scope="col">Actions</th></tr></thead>
                <tbody id="users-table">
                    @foreach ($users as $user)
                        <tr data-user-row data-search="{{ strtolower($user['name'].' '.$user['email']) }}">
                            <td><div class="d-flex align-items-center gap-3"><span class="user-avatar" aria-hidden="true">{{ strtoupper(substr($user['name'], 0, 1)) }}</span><div><div class="fw-semibold">{{ $user['name'] }}</div><div class="text-muted small">{{ $user['email'] }}</div></div></div></td>
                            <td>
                                @if ($user['role'] === 'admin')
                                    <span class="badge rounded-pill user-status">Admin</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.action', $user['id']) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="role">
                                        <select class="form-select form-select-sm role-select" name="role" aria-label="Role for {{ $user['name'] }}" onchange="this.form.submit()">
                                            @foreach (['client' => 'Client', 'artist' => 'Artist'] as $value => $label)
                                                <option value="{{ $value }}" @selected($user['role'] === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td><span class="badge rounded-pill user-status" data-status="{{ $user['status'] }}">{{ $user['status'] }}</span></td>
                            <td class="text-nowrap">{{ $user['joined'] }}</td>
                            <td class="user-actions">
                                @if ($user['role'] === 'admin')
                                    <span class="text-muted small">Protected account</span>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('admin.users.action', $user['id']) }}" class="user-action-form">
                                            @csrf<input type="hidden" name="action" value="status">
                                            <button type="submit" class="btn btn-sm btn-outline-likha rounded-pill">{{ $user['status'] === 'Active' ? 'Suspend' : 'Reactivate' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.action', $user['id']) }}" class="user-action-form" data-delete-form>
                                            @csrf<input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-outline-likha rounded-pill">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-muted text-center py-4 mb-0 d-none" id="empty-results">No users match that search.</p>
        </div>
    </section>
    <p class="text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Account role and status changes are saved to the platform database.</p>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const search = document.getElementById('user-search');
        const rows = [...document.querySelectorAll('[data-user-row]')];
        const emptyResults = document.getElementById('empty-results');

        search.addEventListener('input', () => {
            const query = search.value.trim().toLowerCase();
            const visible = rows.filter((row) => {
                const matches = row.dataset.search.includes(query);
                row.classList.toggle('d-none', !matches);
                return matches;
            }).length;
            emptyResults.classList.toggle('d-none', visible !== 0);
        });

        document.querySelectorAll('[data-delete-form]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm('Delete this marketplace account?')) event.preventDefault();
            });
        });
    })();
</script>
@endpush
