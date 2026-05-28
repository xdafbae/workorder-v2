<?php

namespace App\Services;

use App\Models\Rule;
use Illuminate\Support\Collection;

class RuleEngineService
{
    public function evaluate(array $symptomIds): Collection
    {
        $selected = collect($symptomIds)->map(fn ($id) => (int) $id)->unique()->values();

        if ($selected->isEmpty()) {
            return collect();
        }

        return Rule::query()
            ->where('is_active', true)
            ->with(['symptoms:id,code,name', 'indications.suggestions'])
            ->get()
            ->flatMap(function (Rule $rule) use ($selected) {
                $ruleSymptomIds = $rule->symptoms->pluck('id');

                if ($ruleSymptomIds->isEmpty()) {
                    return [];
                }

                $matched = $ruleSymptomIds->intersect($selected)->count();
                $ratio = $matched / $ruleSymptomIds->count();

                if ($matched === 0 || $ratio < 0.5) {
                    return [];
                }

                $score = (int) round($rule->weight * $ratio);

                return $rule->indications->map(function ($indication) use ($rule, $matched, $ratio, $score) {
                    $indication->setAttribute('matched_symptoms', $matched);
                    $indication->setAttribute('match_ratio', $ratio);
                    $indication->setAttribute('score', $score);
                    $indication->setAttribute('rule_name', $rule->name);

                    return $indication;
                });
            })
            ->sortByDesc('score')
            ->unique('id')
            ->values();
    }
}
