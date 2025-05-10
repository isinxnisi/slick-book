<?php

use Modules\ContentModule\Samples\Domain\Strategies\Collection;
use Modules\ContentModule\Samples\Domain\Strategies\Series;
use Modules\ContentModule\Samples\Domain\Strategies\Widget;

return [

    /**
     * 利用可能な Strategy クラス群
     */
    'strategies' => [
        // Collection
        Collection\StaticCollectionStrategy::class,
        Collection\DynamicCollectionStrategy::class,
        // Series
        Series\ManualSeriesStrategy::class,
        Series\AutoSeriesStrategy::class,
        // Widget
        Widget\QuizWidgetStrategy::class,
    ],

];
