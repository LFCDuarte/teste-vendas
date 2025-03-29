<!DOCTYPE html>
<html>
<head>
    <title>Resumo da Venda #{{ $venda->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { margin-top: 20px; text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resumo da Venda #{{ $venda->id }}</h1>
    </div>

    <div class="info">
        <p><strong>Data:</strong> {{ $venda->data_venda->format('d/m/Y') }}</p>
        <p><strong>Cliente:</strong> {{ $venda->cliente->nome }}</p>
        <p><strong>Vendedor:</strong> {{ $venda->vendedor->name }}</p>
        <p><strong>Status:</strong> {{ $venda->status_formatado }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venda->vendaProdutos as $item)
            <tr>
                <td>{{ $item->produto->nome }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total: {{ $venda->valor_total_formatado }}</p>
    </div>
</body>
</html>
