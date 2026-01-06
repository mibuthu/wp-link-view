<?php // phpcs:disable
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        FunctionFirstClassCallableRector::class,
    ])
    // ->withPhpSets()
    // ->withTypeCoverageLevel(0)
    // ->withDeadCodeLevel(0)
    // ->withCodeQualityLevel(0)
;