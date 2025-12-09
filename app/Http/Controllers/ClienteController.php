<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

   public function store(Request $request)
    {
        // Validaciones para campos básicos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|size:13|regex:/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/|unique:clientes',
            'contrasena_fiel' => 'required|string|min:6',
        ], [
            'rfc.size' => 'El RFC debe tener exactamente 13 caracteres.',
            'rfc.regex' => 'El formato del RFC no es válido. Ejemplo: XAXX010101000',
        ]);

        // Validación personalizada para el certificado (.cer)
        $certificadoValidator = Validator::make($request->all(), [
            'certificado' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowed = ['cer', 'pem'];
                    if (!in_array($extension, $allowed)) {
                        $fail('El certificado debe ser un archivo .cer o .pem');
                    }
                }
            ]
        ]);

        if ($certificadoValidator->fails()) {
            return redirect()->back()
                ->withErrors($certificadoValidator)
                ->withInput();
        }

        // Validación personalizada para la llave (.key)
        $llaveValidator = Validator::make($request->all(), [
            'llave' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowed = ['key', 'pem', 'txt'];
                    if (!in_array($extension, $allowed)) {
                        $fail('La llave debe ser un archivo .key, .pem o .txt');
                    }
                }
            ]
        ]);

        if ($llaveValidator->fails()) {
            return redirect()->back()
                ->withErrors($llaveValidator)
                ->withInput();
        }

        // Guardar archivos
        $certificadoPath = $request->file('certificado')->store('certificados', 'public');
        $llavePath = $request->file('llave')->store('llaves', 'public');

        // Crear cliente
        Cliente::create([
            'nombre' => $request->nombre,
            'rfc' => strtoupper($request->rfc),
            'contrasena_fiel' => $request->contrasena_fiel,
            'certificado_path' => $certificadoPath,
            'llave_path' => $llavePath
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }


       /**
     * Mostrar detalles de un cliente específico
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Mostrar formulario para editar un cliente
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar un cliente existente
     */
    public function update(Request $request, Cliente $cliente)
    {
        // Validaciones básicas
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|size:13|regex:/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/|unique:clientes,rfc,' . $cliente->id,
            'contrasena_fiel' => 'nullable|string|min:6',
        ], [
            'rfc.size' => 'El RFC debe tener exactamente 13 caracteres.',
            'rfc.regex' => 'El formato del RFC no es válido. Ejemplo: XAXX010101000',
        ]);

        // Inicializar datos a actualizar
        $data = [
            'nombre' => $request->nombre,
            'rfc' => strtoupper($request->rfc),
        ];

        // Solo actualizar contraseña si se proporcionó
        if ($request->filled('contrasena_fiel')) {
            $data['contrasena_fiel'] = $request->contrasena_fiel;
        }

        // Manejar archivos si se suben nuevos
        if ($request->hasFile('certificado')) {
            // Validar extensión del certificado
            $certificadoExt = strtolower($request->file('certificado')->getClientOriginalExtension());
            if (!in_array($certificadoExt, ['cer', 'pem'])) {
                return redirect()->back()
                    ->withErrors(['certificado' => 'El certificado debe ser .cer o .pem'])
                    ->withInput();
            }
            
            // Eliminar archivo anterior
            Storage::disk('public')->delete($cliente->certificado_path);
            
            // Guardar nuevo archivo
            $data['certificado_path'] = $request->file('certificado')->store('certificados', 'public');
        }

        if ($request->hasFile('llave')) {
            // Validar extensión de la llave
            $llaveExt = strtolower($request->file('llave')->getClientOriginalExtension());
            if (!in_array($llaveExt, ['key', 'pem', 'txt'])) {
                return redirect()->back()
                    ->withErrors(['llave' => 'La llave debe ser .key, .pem o .txt'])
                    ->withInput();
            }
            
            // Eliminar archivo anterior
            Storage::disk('public')->delete($cliente->llave_path);
            
            // Guardar nuevo archivo
            $data['llave_path'] = $request->file('llave')->store('llaves', 'public');
        }

        // Actualizar cliente
        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }
    public function destroy(Cliente $cliente)
    {
        // Eliminar archivos
        Storage::disk('public')->delete($cliente->certificado_path);
        Storage::disk('public')->delete($cliente->llave_path);
        
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}