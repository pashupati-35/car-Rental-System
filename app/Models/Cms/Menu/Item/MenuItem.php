<?php

namespace App\Models\Cms\Menu\Item;

use App\Http\Resources\Cms\Menu\Item\MenuItemResource;
use App\Models\Cms\Blog\Blog;
use App\Models\Cms\Download\Download;
use App\Models\Cms\Download\Type\DownloadType;
use App\Models\Cms\Menu\Menu;
use App\Models\Cms\Page\Page;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'page_id',
        'blog_id',
        'title',
        'menu_type',
        'menu_id',
        'menu_location_type',
        'link',
        'position',
        'depth',
        'new_tab',
        'is_active',
    ];

    protected $appends = ['nested'];

    public function getNestedAttribute()
    {
        $nested = [];
        $items = $this->whereParentId($this->id)->orderBy('position')->whereIsActive(1)->get();
        if ($items->count() > 0) {
            return MenuItemResource::collection($items);
        }

        return $nested;

    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function event()
    {
        return $this->belongsTo(Blog::class, 'event_id');
    }

    public function news()
    {
        return $this->belongsTo(Blog::class, 'news_id');
    }

    public function download()
    {
        return $this->belongsTo(Download::class, 'download_id');
    }

    public function download_type()
    {
        return $this->belongsTo(DownloadType::class, 'download_type_id');
    }
}
