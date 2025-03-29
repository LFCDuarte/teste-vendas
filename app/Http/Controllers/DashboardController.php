<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalClientes = Cliente::count();
        $totalProdutos = Produto::count();
        $totalVendas = Venda::count();

        return view('dashboard', compact('totalClientes', 'totalProdutos', 'totalVendas'));
    }
}
