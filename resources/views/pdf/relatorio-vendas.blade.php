<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Vendas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Vendas</h1>
        <p>Data de geração: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendas as $venda)
            <tr>
                <td>{{ $venda->id }}</td>
                <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                <td>{{ $venda->cliente->nome }}</td>
                <td>{{ $venda->vendedor->name }}</td>
                <td>{{ $venda->status_formatado }}</td>
                <td>{{ $venda->valor_total_formatado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
