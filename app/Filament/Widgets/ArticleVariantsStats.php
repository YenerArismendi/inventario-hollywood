<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArticleVariantsStats extends BaseWidget
{
    public ?Article $record = null;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return true;
    }

    public function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        return [
            Stat::make('Variantes', $this->record->variants()->count())
                ->description('Número de variantes del artículo')
                ->icon('heroicon-o-rectangle-stack'),
        ];
    }
}
