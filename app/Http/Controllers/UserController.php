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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $validator = Validator::make($request->all(), [
            'picture' => 'required|file|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'changePhoto')->withInput();
        }

        try {
            $userId = Auth::id();
            $file = $request->file('picture');
            $destination = public_path('images/profile');

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower((string) $file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors([
                    'picture' => 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.',
                ], 'changePhoto');
            }

            if (@getimagesize($file->getRealPath()) === false) {
                return redirect()->back()->withErrors([
                    'picture' => 'The selected file is not a valid image.',
                ], 'changePhoto');
            }

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            if (!is_writable($destination)) {
                return redirect()->back()->withErrors([
                    'picture' => 'Profile image folder is not writable.',
                ], 'changePhoto');
            }

            // Save profile image as a normalized square to avoid stretched/crushed avatars.
            $imageName = 'profile_'.$userId.'_'.time().'_'.Str::random(6).'.jpg';
            $imagePath = $destination.DIRECTORY_SEPARATOR.$imageName;
            $stored = false;

            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo && function_exists('imagecreatetruecolor')) {
                $source = null;
                switch ($imageInfo['mime']) {
                    case 'image/jpeg':
                        $source = @imagecreatefromjpeg($file->getRealPath());
                        break;
                    case 'image/png':
                        $source = @imagecreatefrompng($file->getRealPath());
                        break;
                    case 'image/gif':
                        $source = @imagecreatefromgif($file->getRealPath());
                        break;
                    case 'image/webp':
                        if (function_exists('imagecreatefromwebp')) {
                            $source = @imagecreatefromwebp($file->getRealPath());
                        }
                        break;
                }

                if ($source) {
                    $srcWidth = imagesx($source);
                    $srcHeight = imagesy($source);
                    $side = min($srcWidth, $srcHeight);
                    $srcX = (int) (($srcWidth - $side) / 2);
                    $srcY = (int) (($srcHeight - $side) / 2);

                    $targetSize = 300;
                    $target = imagecreatetruecolor($targetSize, $targetSize);

                    // White background for compatibility with non-transparent formats.
                    $white = imagecolorallocate($target, 255, 255, 255);
                    imagefill($target, 0, 0, $white);

                    imagecopyresampled(
                        $target,
                        $source,
                        0,
                        0,
                        $srcX,
                        $srcY,
                        $targetSize,
                        $targetSize,
                        $side,
                        $side
                    );

                    $stored = imagejpeg($target, $imagePath, 90);
                    imagedestroy($target);
                    imagedestroy($source);
                }
            }

            if (!$stored) {
                $imageName = 'profile_'.$userId.'_'.time().'_'.Str::random(6).'.'.$extension;
                $file->move($destination, $imageName);
            }

            User::where('id', $userId)->update(['picture' => $imageName]);

            return redirect()->back()->with('photo_success', 'Profile picture updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Profile picture upload failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'picture' => 'Unable to upload profile picture. Please try again.',
            ], 'changePhoto');
        }
    }
}
