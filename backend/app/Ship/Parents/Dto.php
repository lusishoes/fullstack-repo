<?php

declare(strict_types=1);

namespace App\Ship\Parents;

use App\Ship\Pipelines\NormalizeBooleanQueryValuesDataPipe;
use Override;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataPipeline;

class Dto extends Data
{
    #[Override]
    public static function pipeline(): DataPipeline
    {
        return parent::pipeline()
            ->firstThrough(NormalizeBooleanQueryValuesDataPipe::class);
    }
}
