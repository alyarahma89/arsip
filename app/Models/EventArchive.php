<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EventArchive extends Model
{
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
