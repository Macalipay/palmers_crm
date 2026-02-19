<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Role;
use App\Division;
use App\Branch;
use App\ModelHasRoles;
use App\TelemarketingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;

class UserController extends Controller
{
    public function index() {
        $role = Role::get();
        $divisions = Division::where('active', 1)->get();
        $branches = Branch::where('active', 1)->get();
        return view('backend.pages.user_management.user', compact('role', 'divisions', 'branches'));
    }

    public function get() {
        if(request()->ajax()) {
            return datatables()->of(User::with('division', 'branch')->get())
            ->addIndexColumn()
            ->make(true);
        }
    }

    function resetPassword($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->password = Hash::make('P@ssw0rd');

            $user->save();

            return "Password has been reset successfully.";
        } else {
            return "User not found.";
        }
    }

    public function save(Request $request) {
        $validate = $request->validate([
            'name' => ['required', 'max:250'],
            'designation' => ['required', 'max:250'],
            'email' => ['required', 'max:250'],
            'active' => ['required', 'max:250'],
        ]);

        $request['password'] = Hash::make('P@ssw0rd');
        $request['created_by'] = Auth::user()->id;
        $request['updated_by'] = Auth::user()->id;
        $request['active'] = 1;

        $user = User::create($request->except(['role_id']));

        $data = array(
            'role_id' => $request->role_id,
            'model_type' => 'App\User',
            'model_id' => $user->id,
        );

        ModelHasRoles::insert($data);

        return response()->json(compact('validate'));
    }

    public function edit($id)
    {
        $data = User::with('role')->where('id', $id)->orderBy('id')->firstOrFail();
        return response()->json(compact('data'));
    }

    public function update(Request $request, $id)
    {
        User::find($id)->update($request->except('_token'));
        ModelHasRoles::where('model_id', $id)->update(['role_id' => $request->role_id]);

        if ($request->role_id == 3 && $request->active == 3) {
           TelemarketingDetail::where('assigned_to', $id)->update(['assigned_to' => 1]);
           return response()->json(['message' => 'RESIGN']);
        } else {
            return response()->json();
        }

    }

    public function destroy($id)
    {
        $record = User::find($id);
        $record->delete();

        ModelHasRoles::where('model_id', $id)->delete();

        return response()->json();
    }
    
    public function changepass(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(Auth::user()->id)->update(['password'=> Hash::make($request->new_password)]);

        Auth::logout();
        return redirect('/login');
    }

    public function changePicture(Request $request)
    {
        $request->validate([
            'picture' => 'required',
        ]);
        $file = $request->picture->getClientOriginalName();
        $filename = pathinfo($file, PATHINFO_FILENAME);

        $imageName = $filename.time().'.'.$request->picture->extension();  
        $picture = $request->picture->move(public_path('images/profile'), $imageName);

        $requestData = $request->all();
        $requestData['picture'] = $imageName;

        User::find($request->user_id)->update(['picture'=> $imageName]);
        
        return redirect()->back()->with('success','Successfully Updated');
    }
}
