<?php

namespace App\Models\Cms\Menu;

use App\Models\Cms\Menu\Item\MenuItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'menu_type',
        'header',
        'is_active',
        'position',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('position', 'ASC');
    }

    protected static function booted()
    {
        static::deleting(function ($menu) {
            $menu->items()->delete();
        });
    }
}
