<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

//用户策略文件
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * $currentUser默认为当前登录用户实例。
     * $user是需要进行授权的用户实例。
     * 当两个用户id相同时，则代表两个用户是相同用户，用户同构授权，可以接着进行下一个操作。
     * 如果id不相同的话，会抛出403异常信息来拒绝访问。
     * 1、我们并不需要验证 $currentUser 是不是NULL。为登录用户，框架会自动为其所有权限返回false；
     * 2、调用时，默认情况下，我们不需要传递当前登录用户至该方法内，因为框架会自动加载当前登录用户；
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    //只有是管理员且删除的不是自己账户，才可以执行删除操作。
    public function destroy(User $currentUser,User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }

    /**
     * 限制自己不能关注自己
     */
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
