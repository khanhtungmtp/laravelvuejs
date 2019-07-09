<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * bảo vệ api , phải đăng nhập mới được lấy
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isAuthor'))
        {
            // get user giá trị về
            return User::latest()->paginate(1);
        }
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        // update user đang đăng nhập
        $user = auth('api')->user();

        $this->validate($request, [
            'name'     => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|min:6'
        ]);

        // nếu thay đổi mật khẩu
        if (!empty($request->password))
        {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $currentPhoto = $user->photo;
        if ($request->photo != $currentPhoto)
        {
            // image/jpeg
            $extention = explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1];
            $name      = time() . '.' . explode('/', $extention)[1];
            \Image::make($request->photo)->save(public_path('img/profile/') . $name);
            // Hàm merge gộp mảng đã cho vào collection, Bất kì string key trong mảng trùng
            // với string key trong collection sẽ bị ghi đè bởi giá trị của collection
            $request->merge(['photo' => $name]);
            // xóa hình cũ
            $userPhoto = public_path('img/profile/').$currentPhoto;
            if (file_exists($userPhoto)){
                @unlink($userPhoto);
            }
           // dd();die;
        }

        $user->update($request->all());
        return ['message' => 'Success'];

    }

    public function search(Request $request)
    {
        // search theo tên hoặc email
        if ($search = $request->get('q')) {
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                      ->orWhere('email','LIKE',"%$search%");
            })->paginate(20);
        }
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);
        // post user
        return User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'type'     => $request->type,
            'bio'      => $request->bio,
            'photo'    => $request->photo,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // sometimes là khi sửa password nó mới validation
        $user = User::findOrfail($id);

        $this->validate($request, [
            'name'     => 'required|string|max:191',
            'email'    => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:6'
        ]);

        $user->update($request->all());

        return ['message' => 'Update the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('isAdmin');
        // phân quyền cách 2 dùng gate của laravel
        if (Gate::allows('isAdmin'))
        {
            // tìm id và xóa user
            $user = User::findOrfail($id);
            $user->delete();
            return ['message' => 'User deleted'];
        }
    }
}
