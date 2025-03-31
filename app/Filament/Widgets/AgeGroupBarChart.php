<?php

namespace App\Filament\Widgets;

use App\Models\Applicant;
use Filament\Widgets\ChartWidget;

class AgeGroupBarChart extends ChartWidget
{
    protected static ?string $heading = 'Applicants by Age Group';

    protected function getData(): array
    {
        $ageGroupCounts = Applicant::query()
            ->whereNotNull('age_groups')
            ->where('age_groups', '!=', '')
            ->pluck('age_groups')
            ->flatMap(
                fn($str) =>
                collect(explode(',', $str))
                    ->map(fn($g) => strtoupper(trim($g)))
            )
            ->filter()
            ->countBy()
            ->sortKeysUsing(function ($a, $b) {
                $numA = (int) preg_replace('/\D/', '', $a);
                $numB = (int) preg_replace('/\D/', '', $b);
                return $numA <=> $numB;
            });

        return [
            'datasets' => [
                [
                    'label' => 'Applicants',
                    'data' => $ageGroupCounts->values(),
                ],
            ],
            'labels' => $ageGroupCounts->keys(),

        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
