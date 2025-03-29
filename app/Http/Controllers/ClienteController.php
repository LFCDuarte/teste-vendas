<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefone' => 'required|string|max:20',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('status', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefone' => 'required|string|max:20',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('status', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        try {
            // Verifica se o cliente está em alguma venda
            if ($cliente->vendas()->exists()) {
                return back()->with('error', 'Não é possível excluir este cliente pois ele possui vendas registradas. Para manter o histórico, considere desativar o cliente ao invés de excluí-lo.');
            }

            $cliente->delete();
            return redirect()->route('clientes.index')->with('status', 'Cliente excluído com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Não foi possível excluir o cliente. Ele pode estar sendo usado em outras partes do sistema.');
        }
    }

    public function toggleAtivo(Cliente $cliente)
    {
        $cliente->ativo = !$cliente->ativo;
        $cliente->save();

        $status = $cliente->ativo ? 'ativado' : 'desativado';
        return back()->with('status', "Cliente {$status} com sucesso!");
    }
}
