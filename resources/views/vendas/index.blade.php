@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendas.index') }}" method="GET" id="filtroForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="cliente" class="form-label">Cliente</label>
                                <input type="text" class="form-control" id="cliente" name="cliente"
                                       value="{{ request('cliente') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="vendedor" class="form-label">Vendedor</label>
                                <input type="text" class="form-control" id="vendedor" name="vendedor"
                                       value="{{ request('vendedor') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="paga" {{ request('status') == 'paga' ? 'selected' : '' }}>Paga</option>
                                    <option value="vencida" {{ request('status') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                                <select class="form-select" id="forma_pagamento" name="forma_pagamento">
                                    <option value="">Todas</option>
                                    <option value="dinheiro" {{ request('forma_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="pix" {{ request('forma_pagamento') == 'pix' ? 'selected' : '' }}>Pix</option>
                                    <option value="debito" {{ request('forma_pagamento') == 'debito' ? 'selected' : '' }}>Débito</option>
                                    <option value="credito" {{ request('forma_pagamento') == 'credito' ? 'selected' : '' }}>Crédito</option>
                                    <option value="boleto" {{ request('forma_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                       value="{{ request('data_inicio') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim"
                                       value="{{ request('data_fim') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="valor_minimo" class="form-label">Valor Mínimo</label>
                                <input type="text" class="form-control money" id="valor_minimo" name="valor_minimo"
                                       value="{{ request('valor_minimo') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="valor_maximo" class="form-label">Valor Máximo</label>
                                <input type="text" class="form-control money" id="valor_maximo" name="valor_maximo"
                                       value="{{ request('valor_maximo') }}">
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-eraser me-1"></i> Limpar Filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Vendas</h5>
                    <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nova Venda
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Forma de Pagamento</th>
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
                                    <td>{{ $venda->vendedor->name }}</td>
                                    <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                                    <td>{{ $venda->valor_total_formatado }}</td>
                                    <td>
                                        @switch($venda->forma_pagamento)
                                            @case('dinheiro')
                                                <span class="badge bg-success">Dinheiro</span>
                                                @break
                                            @case('pix')
                                                <span class="badge bg-info">Pix</span>
                                                @break
                                            @case('debito')
                                                <span class="badge bg-primary">Débito</span>
                                                @break
                                            @case('credito')
                                                <span class="badge bg-warning">Crédito</span>
                                                @break
                                            @case('boleto')
                                                <span class="badge bg-secondary">Boleto</span>
                                                @break
                                        @endswitch
                                    </td>
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
                                        <a href="{{ route('vendas.pdf', $venda->id) }}" class="btn btn-primary btn-sm" title="Gerar PDF">
                                            <i class="fas fa-file-pdf"></i>
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
                                    <td colspan="9" class="text-center">Nenhuma venda registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.money').mask('R$ #.##0,00', {
        reverse: true,
        placeholder: 'R$ 0,00'
    });
});
</script>
@endpush

@endsection
