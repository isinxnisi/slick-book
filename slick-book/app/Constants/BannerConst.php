<?php

namespace App\Constants;

class BannerConst
{
    public const DEVICE_PC_TABLET = 'pc_tablet';
    public const DEVICE_MOBILE    = 'mobile';

    public const SECTION_CENTER = 'center';
    public const SECTION_RSIDE  = 'rside';
    public const SECTION_FOOTER = 'footer';

    public const SECTION_LIST = [
        self::SECTION_CENTER => 'センター',
        self::SECTION_RSIDE  => '右サイドバー',
        self::SECTION_FOOTER => 'フッター',
    ];

    public const SLOT_LIST = [
        self::SECTION_CENTER => [
            1 => 'TOP',
            2 => '本文下',
        ],
        self::SECTION_RSIDE => [
            1 => 'TOP',
            2 => '中段',
            3 => '下部',
        ],
        self::SECTION_FOOTER => [
            1 => '上部',
        ],
    ];
}
