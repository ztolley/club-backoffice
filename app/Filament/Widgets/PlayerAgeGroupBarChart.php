<?php

namespace App\Filament\Widgets;

use App\Models\Player;
use Filament\Widgets\ChartWidget;

class PlayerAgeGroupBarChart extends ChartWidget
{
    protected static ?string $heading = 'Players by Team';

    protected function getData(): array
    {
        // Get player counts grouped by team name
        $teamCounts = Player::query()
            ->whereNotNull('team_id')
            ->with('team')
            ->get()
            ->groupBy(fn($player) => $player->team?->name ?? 'No Team')
            ->map(fn($group) => $group->count())
            ->sortDesc();

        // Generate a color for each team
        $colors = [
            '#6366f1',
            '#f59e42',
            '#10b981',
            '#f43f5e',
            '#fbbf24',
            '#3b82f6',
            '#a78bfa',
            '#f472b6',
            '#34d399',
            '#f87171',
            '#60a5fa',
            '#facc15',
            '#4ade80',
            '#f472b6',
            '#818cf8',
            // Add more colors if you have more teams
        ];
        $backgroundColors = array_slice($colors, 0, $teamCounts->count());

        return [
            'datasets' => [
                [
                    'label' => 'Players',
                    'data' => $teamCounts->values(),
                    'backgroundColor' => $backgroundColors,
                    'offset' => 30,
                ],
            ],
            'labels' => $teamCounts->map(fn($count, $team) => "{$team}: {$count}")->values(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right', // or 'bottom' for horizontal legend
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
