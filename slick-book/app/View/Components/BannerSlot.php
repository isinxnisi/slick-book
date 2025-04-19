<?php

namespace App\View\Components\Blog\Ad;

use Illuminate\View\Component;

class BannerSlot extends Component
{
    public string $section;
    public int $slotNo;
    public string $marginClass;

    public function __construct(string $section, int $slotNo = 1, string $marginClass = 'mb-4')
    {
        $this->section = $section;
        $this->slotNo = $slotNo;
        $this->marginClass = $marginClass;
    }

    public function render()
    {
        return view('components.blog.ad.banner-slot');
    }
}
