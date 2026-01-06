<?php // phpcs:disable
declare(strict_types=1);

use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Fsylum\RectorWordPress\Set\WordPressSetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets()
    ->withAttributesSets()
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        deadCode: true,
        instanceOf: true,
        earlyReturn: true,
        naming: true,
        privatization: true,
        rectorPreset: true,
        typeDeclarations: true,
    )
    ->withSets([
        WordPressSetList::WP_6_8
    ])
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        DisallowedEmptyRuleFixerRector::class,
        NewlineAfterStatementRector::class,
    ])
;