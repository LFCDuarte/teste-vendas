@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2 class="mb-4">Seja bem-vindo(a), {{ Auth::user()->name }}!</h2>

                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('clientes.index') }}" class="text-decoration-none">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="card-title mb-0">Clientes</h5>
                                                <p class="card-text mb-0">Gerencie seus clientes</p>
                                            </div>
                                            <h3 class="mb-0">{{ $totalClientes }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('produtos.index') }}" class="text-decoration-none">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="card-title mb-0">Produtos</h5>
                                                <p class="card-text mb-0">Controle seu estoque</p>
                                            </div>
                                            <h3 class="mb-0">{{ $totalProdutos }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('vendas.index') }}" class="text-decoration-none">
                                <div class="card bg-info text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="card-title mb-0">Vendas</h5>
                                                <p class="card-text mb-0">Acompanhe suas vendas</p>
                                            </div>
                                            <h3 class="mb-0">{{ $totalVendas }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
