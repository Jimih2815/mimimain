<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryGroup;
use App\Models\MenuSection;
use App\Models\MenuItem;

class BackfillMenuGroupId extends Command
{
    // 1) đây là signature phải khớp với lệnh
    protected $signature = 'backfill:menu_group_id';

    // 2) mô tả tuỳ ý
    protected $description = 'Điền lại menu_group_id cho các CategoryGroup còn NULL';

    public function handle()
    {
        $this->info('Bắt đầu back-fill menu_group_id…');

        CategoryGroup::whereNull('menu_group_id')
            ->chunkById(100, function ($groups) {
                foreach ($groups as $grp) {
                    DB::transaction(function () use ($grp) {
                        $sec = MenuSection::firstOrCreate(
                            ['slug'       => $grp->category->slug],
                            ['name'       => $grp->category->name,
                             'sort_order' => $grp->category->sort_order ?? 0]
                        );

                        $item = MenuItem::create([
                            'menu_section_id' => $sec->id,
                            'label'           => $grp->title,
                            'url'             => '#',
                            'sort_order'      => $grp->sort_order,
                        ]);

                        $grp->updateQuietly(['menu_group_id' => $item->id]);
                    });
                }
            });

        $this->info('✅ Xong! Tất cả CategoryGroup đã có menu_group_id.');
    }
}
