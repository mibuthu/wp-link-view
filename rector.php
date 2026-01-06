<?php // phpcs:disable
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Fsylum\RectorWordPress\Set\WordPressSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets()
    ->withAttributesSets()
    ->withPreparedSets(
        codeQuality: false,
        codingStyle: false,
        deadCode: true,
        instanceOf: false,
        earlyReturn: false,
        naming: false,
        privatization: false,
        rectorPreset: false,
        strictBooleans: false,
        typeDeclarations: true,
    )
    ->withSets([
        WordPressSetList::WP_6_8
    ])
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
    ])
;