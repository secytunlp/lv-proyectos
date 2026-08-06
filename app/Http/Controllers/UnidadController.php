<?php

namespace App\Http\Controllers;

use App\Traits\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Unidad;

class UnidadController extends Controller
{
    use SanitizesInput;

    function __construct()
    {
        $this->middleware('permission:unidad-listar|unidad-crear|unidad-editar|unidad-eliminar', ['only' => ['index', 'show']]);
        $this->middleware('permission:unidad-crear', ['only' => ['create', 'store']]);
        $this->middleware('permission:unidad-editar', ['only' => ['edit', 'update']]);
        $this->middleware('permission:unidad-eliminar', ['only' => ['destroy']]);
    }

    /**
     * Reglas de validación compartidas entre alta y modificación.
     */
    private function rules()
    {
        return [
            'nombre'      => 'required|string|max:255',
            'codigo'      => 'nullable|string|max:15',
            'sigla'       => 'nullable|string|max:15',
            'tipo'        => 'nullable|integer',
            'padre_id'    => 'nullable|exists:unidads,id',
            'facultad_id' => 'nullable|exists:facultads,id',
            'direccion'   => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'telefono'    => 'nullable|string|max:15',
        ];
    }

    /**
     * Normaliza los selects/números vacíos a null para que las reglas
     * "nullable|exists|integer" no fallen con cadenas vacías.
     */
    private function normalize(Request $request)
    {
        $request->merge([
            'padre_id'    => $request->filled('padre_id') ? $request->padre_id : null,
            'facultad_id' => $request->filled('facultad_id') ? $request->facultad_id : null,
            'tipo'        => $request->filled('tipo') ? $request->tipo : null,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidads = Unidad::with('padre')->orderBy('nombre', 'ASC')->get();
        $facultades = DB::table('facultads')->pluck('nombre', 'id');

        return view('unidads.index', compact('unidads', 'facultades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidadesPadre = Unidad::orderBy('nombre', 'ASC')->get();
        $facultades = DB::table('facultads')->pluck('nombre', 'id');

        return view('unidads.create', compact('unidadesPadre', 'facultades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->normalize($request);
        $this->validate($request, $this->rules());

        $input = $this->sanitizeInput($request->except(['_token', 'activa']));
        $input['activa'] = $request->has('activa');

        Unidad::create($input);

        return redirect()->route('unidads.index')
            ->with('success', 'Unidad creada con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $unidad = Unidad::with(['padre', 'hijas'])->findOrFail($id);
        $facultades = DB::table('facultads')->pluck('nombre', 'id');

        return view('unidads.show', compact('unidad', 'facultades'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        // Excluir la propia unidad de la lista de posibles padres.
        $unidadesPadre = Unidad::where('id', '!=', $id)->orderBy('nombre', 'ASC')->get();
        $facultades = DB::table('facultads')->pluck('nombre', 'id');

        return view('unidads.edit', compact('unidad', 'unidadesPadre', 'facultades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->normalize($request);
        $this->validate($request, $this->rules());

        $unidad = Unidad::findOrFail($id);

        $input = $this->sanitizeInput($request->except(['_token', '_method', 'activa']));
        $input['activa'] = $request->has('activa');

        // Evitar que una unidad sea su propio padre.
        if (isset($input['padre_id']) && $input['padre_id'] == $id) {
            $input['padre_id'] = null;
        }

        $unidad->update($input);

        return redirect()->route('unidads.index')
            ->with('success', 'Unidad modificada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unidad = Unidad::findOrFail($id);

        // No permitir eliminar una unidad que tiene unidades hijas asociadas.
        if (Unidad::where('padre_id', $id)->exists()) {
            return redirect()->route('unidads.index')
                ->with('error', 'No se puede eliminar: la unidad tiene unidades hijas asociadas.');
        }

        $unidad->delete();

        return redirect()->route('unidads.index')
            ->with('success', 'Unidad eliminada con éxito');
    }
}
