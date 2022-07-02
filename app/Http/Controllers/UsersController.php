<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 表单验证
     */
    public function store(Request $request)
    {
        //验证表单中的数据
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        //用户完成验证则创建
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth;;login($user);
        session()->flash('success','欢迎，您将在这里开启一段新的路程～');
        //返回用户个人页面
        return redirect()->route('users.show', [$user]);
    }

    /**
     * 编辑
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 修改用户信息
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data =[];
        $data['name'] = $request->name;
        if($request->password)
        {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功');

        return redirect()->route('users.show', $user->id);
    }
}
