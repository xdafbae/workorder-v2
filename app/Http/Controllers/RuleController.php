<?php

namespace App\Http\Controllers;

use App\Models\DamageIndication;
use App\Models\Rule;
use App\Models\Symptom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RuleController extends Controller
{
    public function index(): View
    {
        $rules = Rule::query()->with(['symptoms', 'indications'])->latest()->get();

        return view('admin.rules', [
            'title' => 'Konfigurasi Rule Engine',
            'role' => 'Super Admin',
            'active' => 'rules',
            'rules' => $rules->map(fn (Rule $rule) => [
                'id' => $rule->id,
                'name' => $rule->name,
                'symptoms' => $rule->symptoms->pluck('code')->join(', '),
                'indication' => $rule->indications->pluck('name')->join(', '),
                'weight' => $rule->weight,
                'active' => $rule->is_active,
            ])->all(),
            'symptoms' => Symptom::query()->orderBy('code')->get(),
            'indicationsList' => DamageIndication::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $rule = Rule::query()->create($data['rule']);
        $rule->symptoms()->sync($data['symptoms']);
        $rule->indications()->sync($data['indications']);

        return back()->with('status', 'Rule berhasil ditambahkan.');
    }

    public function update(Request $request, Rule $rule): RedirectResponse
    {
        $data = $this->validated($request);

        $rule->update($data['rule']);
        $rule->symptoms()->sync($data['symptoms']);
        $rule->indications()->sync($data['indications']);

        return back()->with('status', 'Rule berhasil diperbarui.');
    }

    public function destroy(Rule $rule): RedirectResponse
    {
        $rule->delete();

        return back()->with('status', 'Rule berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'symptoms' => ['required', 'array', 'min:1'],
            'symptoms.*' => ['integer', 'exists:symptoms,id'],
            'indications' => ['required', 'array', 'min:1'],
            'indications.*' => ['integer', 'exists:damage_indications,id'],
        ]);

        return [
            'rule' => [
                'name' => $validated['name'],
                'weight' => $validated['weight'],
                'is_active' => $request->boolean('is_active'),
            ],
            'symptoms' => $validated['symptoms'],
            'indications' => $validated['indications'],
        ];
    }
}
