@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($produto) ? 'Editar Produto' : 'Novo Produto' }}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ isset($produto) ? route('produtos.update', $produto) : route('produtos.store') }}" method="POST">
                        @csrf
                        @if(isset($produto))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $produto->nome ?? '') }}" required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="text" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" value="{{ old('valor', isset($produto) ? $produto->valor_formatado : 'R$ 0,00') }}" required>
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
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
@endsection
