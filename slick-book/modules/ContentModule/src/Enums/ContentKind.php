<?php

namespace Modules\ContentModule\Enums;

enum ContentKind: string
{
    case Article    = 'article';
    case Series     = 'series';
    case Collection = 'collection';
    case Guidebook  = 'guidebook';
}
