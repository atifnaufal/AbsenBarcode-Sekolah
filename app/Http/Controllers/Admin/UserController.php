<?php
namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request, string $role)
    {
        $enum = $role === 'guru' ? UserRole::GURU : UserRole::SISWA;
        $q = $request->input('q');
        $users = User::where('role',$enum->value)->when($q, fn($qq)=>$qq->where(fn($w)=>$w->where('name','ilike',"%$q%")->orWhere('identifier','ilike',"%$q%")->orWhere('email','ilike',"%$q%")))->orderBy('name')->paginate(12)->withQueryString();

        $school = SchoolSetting::first();
        return view('admin.users.index', compact('users','role','q', 'school'));
    }

    public function toggleRegistration(Request $request, string $role)
    {
        $school = SchoolSetting::firstOrFail();
        $field = $role === 'siswa' ? 'registration_enabled_students' : 'registration_enabled_teachers';

        $school->update([
            $field => $request->boolean('enabled')
        ]);

        return back()->with('ok', 'Status pendaftaran ' . $role . ' berhasil diubah.');
    }
    public function create(string $role){ return view('admin.users.form', ['role'=>$role,'user'=>new User(['role'=>UserRole::from($role)])]); }
    public function store(Request $request, string $role){
        $enum = UserRole::from($role);
        $data = $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','identifier'=>'required|string|max:30|unique:users,identifier','class_name'=>'nullable|string|max:50','password'=>'required|min:6']);
        $data['role']=$enum->value; $data['active']=true;
        User::create($data);
        return redirect()->route('admin.users.index',$role)->with('ok','Data berhasil ditambah');
    }
    public function edit(string $role, User $user){ return view('admin.users.form', compact('role','user')); }
    public function update(Request $request, string $role, User $user){
        $data=$request->validate(['name'=>'required|string|max:100','email'=>['required','email',Rule::unique('users')->ignore($user->id)],'identifier'=>['required','string','max:30',Rule::unique('users')->ignore($user->id)],'class_name'=>'nullable|string|max:50','password'=>'nullable|min:6','active'=>'boolean']);
        if(empty($data['password'])) unset($data['password']);
        $data['active']=$request->boolean('active');
        $user->update($data);
        return redirect()->route('admin.users.index',$role)->with('ok','Data diperbarui');
    }
    public function destroy(string $role, User $user){ $user->delete(); return back()->with('ok','Data dihapus'); }
}
