@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Vendas</h5>
                    <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nova Venda
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Parcelas</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendas as $venda)
                                    <tr>
                                        <td>{{ $venda->id }}</td>
                                        <td>{{ $venda->cliente->nome }}</td>
                                        <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                                        <td>{{ $venda->valor_total_formatado }}</td>
                                        <td>{{ $venda->parcelas->count() }}x de {{ $venda->parcelas->first()?->valor_formatado }}</td>
                                        <td>
                                            <span class="badge bg-{{ $venda->status_color }}">
                                                {{ $venda->status_formatado }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-info" title="Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($venda->status === 'pendente')
                                                <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('vendas.destroy', $venda) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja cancelar esta venda?')" title="Cancelar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhuma venda registrada.</td>
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
