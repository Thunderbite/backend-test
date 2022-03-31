<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['campaign_id', 'prize_id', 'account', 'revealed_at'];

    protected $casts = [
        'revealed_at' => 'datetime',
    ];

    public static function filter()
    {
        $query = self::query();
        $campaign = Campaign::find(session('activeCampaign'));

        self::filterDates($query, $campaign);

        if ($data = request('filter1')) {
            $query->where('account', 'like', $data.'%');
        }

        if ($data = request('filter2')) {
            $query->where('prizeId', $data);
        }

        if ($data = request('filter3')) {
            $query->whereRaw('HOUR(revealed_at) >= '.$data);
        }

        if ($data = request('filter4')) {
            $query->whereRaw('HOUR(revealed_at) <= '.$data);
        }

        $query->leftJoin('prizes', 'prizes.id', '=', 'games.prizeId')
            ->select('games.id', 'account', 'prizeId', 'revealed_at', 'prizes.title')
            ->where('games.campaign_id', $campaign->id);

        return $query;
    }

    private static function filterDates($query, $campaign): void
    {
        if (($data = request('date_start')) || ($data = Carbon::now()->subDays(6))) {
            $data = Carbon::parse($data)->setTimezone($campaign->timezone)->toDateTimeString();
            $query->where('games.revealed_at', '>=', $data);
        }
        if (($data = request('date_end')) || ($data = Carbon::now())) {
            $data = Carbon::parse($data)->setTimezone($campaign->timezone)->toDateTimeString();
            $query->where('games.revealed_at', '<=', $data);
        }
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }
}
