<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas de Clientes
    Route::resource('clientes', ClienteController::class);
    Route::patch('/clientes/{cliente}/toggle-ativo', [ClienteController::class, 'toggleAtivo'])->name('clientes.toggle-ativo');

    // Rotas de Produtos
    Route::resource('produtos', ProdutoController::class);
    Route::patch('/produtos/{produto}/toggle-ativo', [ProdutoController::class, 'toggleAtivo'])->name('produtos.toggle-ativo');

    // Rotas de Vendas
    Route::resource('vendas', VendaController::class);
    Route::patch('parcelas/{parcela}/pagar', [VendaController::class, 'pagarParcela'])->name('vendas.pagar-parcela');
    Route::get('/vendas/{id}/pdf', [VendaController::class, 'downloadPDF'])->name('vendas.pdf');
    Route::get('/vendas/relatorio/pdf', [VendaController::class, 'relatorioVendas'])->name('vendas.relatorio');
});
