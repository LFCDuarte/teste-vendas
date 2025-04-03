@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($venda) ? 'Editar Venda' : 'Nova Venda' }}</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($venda) ? route('vendas.update', $venda) : route('vendas.store') }}" method="POST" id="vendaForm">
                        @csrf
                        @if(isset($venda))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                                    <option value="">Selecione um cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ (old('cliente_id', $venda->cliente_id ?? '') == $cliente->id) ? 'selected' : '' }}>
                                            {{ $cliente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                                <select class="form-select @error('forma_pagamento') is-invalid @enderror" id="forma_pagamento" name="forma_pagamento" required>
                                    <option value="">Selecione a forma de pagamento</option>
                                    <option value="dinheiro" {{ (old('forma_pagamento', $venda->forma_pagamento ?? '') == 'dinheiro') ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="pix" {{ (old('forma_pagamento', $venda->forma_pagamento ?? '') == 'pix') ? 'selected' : '' }}>Pix</option>
                                    <option value="debito" {{ (old('forma_pagamento', $venda->forma_pagamento ?? '') == 'debito') ? 'selected' : '' }}>Débito</option>
                                    <option value="credito" {{ (old('forma_pagamento', $venda->forma_pagamento ?? '') == 'credito') ? 'selected' : '' }}>Crédito</option>
                                    <option value="boleto" {{ (old('forma_pagamento', $venda->forma_pagamento ?? '') == 'boleto') ? 'selected' : '' }}>Boleto</option>
                                </select>
                                @error('forma_pagamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="numero_parcelas" class="form-label">Número de Parcelas</label>
                                <select class="form-select @error('numero_parcelas') is-invalid @enderror" id="numero_parcelas" name="numero_parcelas" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ (old('numero_parcelas', $venda->numero_parcelas ?? 1) == $i) ? 'selected' : '' }}>
                                            {{ $i }}x
                                        </option>
                                    @endfor
                                </select>
                                @error('numero_parcelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Produtos</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addProduto">
                                    <i class="fas fa-plus"></i> Adicionar Produto
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="produtos-container">
                                    @if(isset($venda))
                                        @foreach($venda->vendaProdutos as $vendaProduto)
                                            <div class="row mb-3 produto-row">
                                                <div class="col-md-6">
                                                    <select class="form-select produto-select" name="produtos[{{ $loop->index }}][id]" required>
                                                        <option value="">Selecione um produto</option>
                                                        @foreach($produtos as $produto)
                                                            @if($produto->status === 'ativo')
                                                                <option value="{{ $produto->id }}" data-valor="{{ $produto->valor }}" {{ $vendaProduto->produto_id == $produto->id ? 'selected' : '' }}>
                                                                    {{ $produto->nome }} - R$ {{ number_format($produto->valor, 2, ',', '.') }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control quantidade" name="produtos[{{ $loop->index }}][quantidade]" value="{{ $vendaProduto->quantidade }}" min="1" placeholder="Quantidade" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remover-produto">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="produto-template">
    <div class="row produto-row mb-3">
        <div class="col-md-6">
            <select class="form-select produto-select" name="produtos[__index__][id]" required>
                <option value="">Selecione um produto</option>
                @foreach($produtos as $produto)
                    @if($produto->status === 'ativo')
                        <option value="{{ $produto->id }}" data-valor="{{ $produto->valor }}">
                            {{ $produto->nome }} - R$ {{ number_format($produto->valor, 2, ',', '.') }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control quantidade-input" name="produtos[__index__][quantidade]" min="1" required placeholder="Quantidade">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remover-produto">Remover</button>
        </div>
    </div>
</template>

@push('scripts')
<script>
    $(document).ready(function() {
        const container = $('#produtos-container');
        const template = $('#produto-template');
        const addButton = $('#addProduto');
        const formaPagamentoSelect = $('#forma_pagamento');
        const numeroParcelasSelect = $('#numero_parcelas');
        let produtoIndex = {{ isset($venda) ? $venda->vendaProdutos->count() : 0 }};

        
        function updateParcelasField() {
            const formaPagamento = formaPagamentoSelect.val();
            const parcelasSemParcelamento = ['dinheiro', 'pix', 'debito'];
            
            if (parcelasSemParcelamento.includes(formaPagamento)) {
                numeroParcelasSelect.val('1').prop('disabled', true);
                numeroParcelasSelect.prop('required', true);
            } else {
                numeroParcelasSelect.prop('disabled', false);
            }
        }

        
        updateParcelasField();

        
        formaPagamentoSelect.on('change', updateParcelasField);

        
        addButton.on('click', function() {
            const produtoHtml = template.html().replace(/__index__/g, produtoIndex++);
            container.append(produtoHtml);
        });

        
        container.on('click', '.remover-produto', function() {
            const row = $(this).closest('.produto-row');
            if (container.find('.produto-row').length > 1) {
                row.remove();
                
                container.find('.produto-row').each(function(index) {
                    $(this).find('[name^="produtos["]').each(function() {
                        const name = $(this).attr('name').replace(/produtos\[\d+\]/, 'produtos[' + index + ']');
                        $(this).attr('name', name);
                    });
                });
            } else {
                alert('A venda deve ter pelo menos um produto.');
            }
        });

        
        if (container.find('.produto-row').length === 0) {
            addButton.click();
        }

        
        $('#vendaForm').on('submit', function(e) {
            
            if (numeroParcelasSelect.prop('disabled')) {
                numeroParcelasSelect.prop('disabled', false);
            }

            const produtos = container.find('.produto-row');
            if (produtos.length === 0) {
                e.preventDefault();
                alert('Adicione pelo menos um produto à venda.');
                return false;
            }

            let valid = true;
            let formData = [];
            produtos.each(function(index) {
                const produto = $(this).find('.produto-select').val();
                const quantidade = $(this).find('.quantidade').val();

                formData.push({
                    index: index,
                    produto: produto,
                    quantidade: quantidade
                });

                if (!produto || !quantidade || quantidade < 1) {
                    valid = false;
                    return false;
                }
            });

            console.log('Dados do formulário:', {
                cliente_id: $('#cliente_id').val(),
                forma_pagamento: $('#forma_pagamento').val(),
                numero_parcelas: $('#numero_parcelas').val(),
                produtos: formData
            });
        });
    });
</script>
@endpush

@endsection
