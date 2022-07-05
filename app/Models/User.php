<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user){
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 用户默认头像加载地址
     */
    public function gravatar($size = '140')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://sdn.geekzu.org/avatar/?s=$size";
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 获取指定用户倒叙的文章
     */
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }

    public function followers()
    {
        //一对多关联模型
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 关注
     */
    public function follow($user_ids)
    {
        if(!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    /**
     * 取消关注
     */
    public function unfollow($user_id)
    {
        if(!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * 当前用户是否关注指定用户
     */
    public function isFollow($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
