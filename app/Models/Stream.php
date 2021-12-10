<?php
namespace App\Models;

class Stream extends MainModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['created_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $connection = 'mysql';
    protected $table = 'streams';
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'user_id', 'user_id');
    }
}
