@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes da Venda #{{ $venda->id }}</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informações da Venda</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Cliente:</dt>
                                <dd class="col-sm-8">{{ $venda->cliente->nome }}</dd>

                                <dt class="col-sm-4">Data:</dt>
                                <dd class="col-sm-8">{{ $venda->created_at->format('d/m/Y H:i') }}</dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $venda->status_color }}">
                                        {{ $venda->status_formatado }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Valor Total:</dt>
                                <dd class="col-sm-8">{{ $venda->valor_total_formatado }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Produtos</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Quantidade</th>
                                            <th>Valor Unitário</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($venda->vendaProdutos as $vendaProduto)
                                            <tr>
                                                <td>{{ $vendaProduto->produto->nome }}</td>
                                                <td>{{ $vendaProduto->quantidade }}</td>
                                                <td>{{ $vendaProduto->valor_unitario_formatado }}</td>
                                                <td>{{ $vendaProduto->valor_total_formatado }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>{{ $venda->valor_total_formatado }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Parcelas</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($venda->parcelas as $parcela)
                                            <tr>
                                                <td>{{ $parcela->numero }}/{{ $venda->parcelas->count() }}</td>
                                                <td>{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                                                <td>{{ $parcela->valor_formatado }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $parcela->status_color }}">
                                                        {{ $parcela->status_formatado }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($parcela->status === 'pendente')
                                                        <form action="{{ route('vendas.pagar-parcela', $parcela) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i> Pagar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                        @if($venda->status === 'pendente')
                            <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            <form action="{{ route('vendas.destroy', $venda) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja cancelar esta venda?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Cancelar Venda
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
