<?php

namespace App\Console\Commands;

use App\Models\Parcela;
use App\Models\Venda;
use Illuminate\Console\Command;

class VerificarParcelasVencidas extends Command
{
    protected $signature = 'vendas:verificar-parcelas';
    protected $description = 'Verifica e atualiza o status das parcelas vencidas e vendas pagas';

    public function handle()
    {
        $this->info('Verificando parcelas vencidas...');

        $parcelas = Parcela::where('status', 'pendente')
            ->where('data_vencimento', '<', now())
            ->get();

        $countParcelas = 0;
        foreach ($parcelas as $parcela) {
            $parcela->verificarStatus();
            $countParcelas++;
        }

        $this->info("Verificação de parcelas concluída. {$countParcelas} parcelas atualizadas.");

        $this->info('Verificando vendas com todas as parcelas pagas...');

        $vendas = Venda::where('status', '!=', 'paga')
            ->whereDoesntHave('parcelas', function ($query) {
                $query->where('status', '!=', 'paga');
            })
            ->get();

        $countVendas = 0;
        foreach ($vendas as $venda) {
            $venda->verificarStatus();
            $countVendas++;
        }

        $this->info("Verificação de vendas concluída. {$countVendas} vendas atualizadas.");
    }
}
