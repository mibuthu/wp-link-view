<?php // phpcs:disable
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php70\Rector\Ternary\TernaryToNullCoalescingRector;
// use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        TernaryToNullCoalescingRector::class
    ])
    // ->withPhpSets()
    // ->withTypeCoverageLevel(0)
    // ->withDeadCodeLevel(0)
    // ->withCodeQualityLevel(0)
;