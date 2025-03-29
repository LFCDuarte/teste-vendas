<?php

namespace App\Console\Commands;

use App\Models\Parcela;
use Illuminate\Console\Command;

class VerificarParcelasVencidas extends Command
{
    protected $signature = 'vendas:verificar-parcelas';
    protected $description = 'Verifica e atualiza o status das parcelas vencidas';

    public function handle()
    {
        $this->info('Verificando parcelas vencidas...');

        $parcelas = Parcela::where('status', 'pendente')
            ->where('data_vencimento', '<', now())
            ->get();

        $count = 0;
        foreach ($parcelas as $parcela) {
            $parcela->verificarStatus();
            $count++;
        }

        $this->info("Verificação concluída. {$count} parcelas atualizadas.");
    }
}
