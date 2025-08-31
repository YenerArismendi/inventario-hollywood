<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Variante;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;


class ArticleVariantsStats extends BaseWidget
{

    use InteractsWithRecord;

    protected function getStats(): array
    {
        $article = $this->getRecord();
        $count = $article ? Variante::where('article_id', $article->id)->count() : 0;

        return [
            Stat::make('Variantes', $count)
                ->description('Asociadas a este artículo')
                ->icon('heroicon-o-rectangle-stack'),
        ];
    }
}

