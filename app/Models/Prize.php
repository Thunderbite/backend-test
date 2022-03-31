<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = ['campaign_id', 'customer_segmentation_id', 'customerlevel_id', 'used', 'section',  'redirect_desktop', 'redirect_mobile', 'win_popup_image', 'nowin_popup_image'];

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('name', 'like', '%'.$query.'%');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function prizes()
    {
        return $this->hasMany(PrizeTable::class, 'prize_id');
    }
}
