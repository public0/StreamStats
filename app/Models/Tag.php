<?php
namespace App\Models;

class Tag extends MainModel
{
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $connection = 'mysql';
    protected $table = 'tags';
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'user_id', 'user_id');
    }

}
