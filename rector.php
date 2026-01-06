<?php // phpcs:disable
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets()
    // ->withTypeCoverageLevel(0)
    // ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(50)
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
    ])
;