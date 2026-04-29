@extends('layouts.app')

@section('title', 'Dashboard — E-ASY POS')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card p-4 text-center">
            <h4 class="fw-bold mb-3">Selamat Datang di E-ASY POS</h4>
            <p class="text-muted">Halo <strong>{{ auth()->user()->name }}</strong>, Anda login sebagai <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>.</p>
            @if(auth()->user()->isCashier())
                <a href="{{ route('pos.index') }}" class="btn btn-primary mt-3"><i class="bi bi-cart"></i> Buka Kasir</a>
            @else
                <a href="{{ route('reports.index') }}" class="btn btn-primary mt-3"><i class="bi bi-graph-up"></i> Lihat Laporan</a>
            @endif
        </div>
    </div>
</div>
@endsection
