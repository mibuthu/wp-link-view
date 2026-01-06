<?php // phpcs:disable
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Identical\StrStartsWithRector;
use Rector\Php80\Rector\Identical\StrEndsWithRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        StrStartsWithRector::class,
        StrEndsWithRector::class,
    ])
    // ->withPhpSets()
    // ->withTypeCoverageLevel(0)
    // ->withDeadCodeLevel(0)
    // ->withCodeQualityLevel(0)
;