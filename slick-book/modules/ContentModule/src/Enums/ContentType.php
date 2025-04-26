<?php

namespace Modules\ContentModule\Enums;

enum ContentType: string
{
    case Slot = 'slot';
    case Layout = 'layout';
    case Static = 'static';
    case System = 'system';
}
