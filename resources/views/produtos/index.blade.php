@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Produtos</h5>
                    <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Novo Produto
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $produto)
                                    <tr>
                                        <td>{{ $produto->id }}</td>
                                        <td>{{ $produto->nome }}</td>
                                        <td>{{ $produto->valor_formatado }}</td>
                                        <td>
                                            <span class="badge bg-{{ $produto->ativo ? 'success' : 'danger' }}">
                                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('produtos.toggle-ativo', $produto) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $produto->ativo ? 'btn-warning' : 'btn-success' }}" title="{{ $produto->ativo ? 'Desativar' : 'Ativar' }}">
                                                        <i class="fas fa-{{ $produto->ativo ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto? Se ele estiver vinculado a vendas, não será possível excluí-lo.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum produto cadastrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
