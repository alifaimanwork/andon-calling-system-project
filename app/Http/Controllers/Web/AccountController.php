<?php

namespace App\Http\Controllers\Web;

use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::getCurrent();

        $viewData = [
            'topBarTitle' => 'MANAGE ACCOUNT',
            'user' => $user
        ];

        return view('pages.web.manage-account.index', $viewData);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Route: account.update

        $user = User::getCurrent();

        // dd($user, $request->all());

        $validated = $request->validate(
            [
                'full_name' => ['required'],
                'email' => [
                    'required',
                    'email',
                    //Rule::unique('users', 'email')->ignore($user->id, 'id')
                ],
                'current_password' => [
                    'required',
                    'current_password'
                ],
                'new_password' => [
                    'nullable',
                    'confirmed',
                    Rules\Password::defaults()
                ]
            ], //TODO: more field
        );

        if ($user->full_name != $validated['full_name']) {
            $user->update(['full_name' => $validated['full_name']]);
            ToastHelper::addToast('User profile updated', 'User Account', 'success', true, ['delay' => 3000]);
        }
        
        if ($user->email != $validated['email']) {
            $user->update(['email' => $validated['email']]);
            ToastHelper::addToast('User profile updated', 'User Account', 'success', true, ['delay' => 3000]);
        }

        if (!is_null($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            ToastHelper::addToast('User profile updated', 'User Account', 'success', true, ['delay' => 3000]);
        }

        return back();
    }

    public function storePicture(Request $request)
    {
        //Route: account.picture.update

        $user = User::getCurrent();

        $request->validate(['profile_picture' => ['image', 'required', 'max:10240', 'mimes:jpg,png']]);


        $max_width = 480;
        $max_height = 480;


        try {

            $image = Image::make($request->file('profile_picture'))
                //->resize($max_width, $max_height, function ($constraint) {
                ->fit($max_width, $max_height, function ($constraint) { //auto size & crop
                    // $constraint->aspectRatio();
                    $constraint->upsize();
                });
        } catch (Exception $e) {
            return redirect()->route('manage-account.index')->withErrors(['profile_picture' => 'Invalid image']);
        }


        // Fixed jpg
        $ext = '.jpg';
        $image->encode('jpg', 80); //jpg quality 80

        // Auto choose png/jpg
        // $image->backup();

        // $imgPng = $image->encode('png');
        // $sizePng = strlen($imgPng->__toString());

        // $image->reset();
        // $imgJpg = $image->encode('jpg', 80);

        // $sizeJpg = strlen($imgJpg->__toString());

        // $ext = '.jpg';
        // if($sizeJpg > $sizePng)
        // {
        //     $image->reset();
        //     $imgPng = $image->encode('png');
        //     $ext = '.png';
        // }

        $hash = sha1($image);

        if (!is_null($user->profile_picture)) {

            if (!User::where('profile_picture', '=', $hash . $ext)->whereNot('id', $user->id)->first())
                Storage::disk('avatars')->delete($user->profile_picture); //delete old profile image
        }
        $user->profile_picture = $hash . $ext;
        $user->save();

        if (!Storage::disk('avatars')->exists($user->profile_picture))
            Storage::disk('avatars')->put($user->profile_picture, $image);

        ToastHelper::addToast('Profile picture updated', 'User Account', 'success', true, ['delay' => 3000]);
        return redirect()->route('manage-account.index');
    }
    public function destroyPicture(Request $request)
    {
        //Route: account.picture.destroy

        $user = User::getCurrent();
        if (!is_null($user->profile_picture)) {
            if (!User::where('profile_picture', '=', $user->profile_picture)->where('id', $user->id)->first())
                Storage::disk('avatars')->delete($user->profile_picture); //delete old profile image
            $user->profile_picture = null;
            $user->save();
        }
        ToastHelper::addToast('Profile picture deleted', 'User Account', 'success', true, ['delay' => 3000]);
        return redirect()->route('manage-account.index');
    }
}
