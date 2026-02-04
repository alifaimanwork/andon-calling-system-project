<?php

namespace App\Http\Controllers\Web\Admin;

use App\Extras\Datasets\UserDataset;
use App\Extras\Utils\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plant;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    //TODO: Resource Guard
    public function index(Request $request)
    {
        $viewData = [
            'topBarTitle' => 'I-POS SETTINGS'
        ];
        return view('pages.web.admin.user.index', $viewData);
    }

    public function create(Request $request)
    {
        //Show create new user page
        //TODO: Check user permission for selected plant
        $plants = Plant::get();
        $companies = Company::get();
        foreach ($plants as $p) {
            $regionName = $p->region->name ?? '-';
            if (!isset($regionPlants[$regionName]))
                $regionPlants[$regionName] = [];

            $regionPlants[$regionName][] = $p;
        }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'regionPlants' => $regionPlants,
            'companies' => $companies,
            'currentUser' => User::getCurrent()
        ];
        return view('pages.web.admin.user.create', $viewData);
    }

    public function store(Request $request)
    {

        //Store new user
        //Simple store db atm

        $validationRules =
            [
                'staff_no' => [
                    'required',
                    Rule::unique(User::TABLE_NAME, 'staff_no')
                ],
                'full_name' => [
                    'required'
                ],
                'sap_id' => [
                    'required'
                ],
                'email' => [
                    'required',
                    'email',

                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults()
                ],
                'company_id' => [
                    'nullable',
                    'exists:App\Models\Company,id'
                ],
                'profile_picture' => [
                    'nullable',
                    'image',
                    File::types(['jpg', 'png'])->max(5 * 1024)
                ]
            ]; //TODO: more field; Permission

        $request->validate($validationRules);
        $data = $request->only(array_keys($validationRules));

        $newUser = new User($data);
        //temp fill
        unset($newUser->profile_picture);

        $newUser->role = $request->input('role');


        $newUser->designation = $request->input('designation');
        $newUser->password = Hash::make($data['password']);
        $newUser->save();

        if ($request->input('plant-access')) {
            if ($request->input('role') == 1 && count($request->input('plant-access')) > 0) {
                foreach ($request->input('plant-access') as $p) {
                    $newUser->plants()->attach($p, ['user_id' => $newUser->id, 'web_permission' => 1, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_ADMIN, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        }

        //store profile picture
        if ($request->profile_picture) {
            $max_width = 480;
            $max_height = 480;

            try {
                $image = Image::make($request->file('profile_picture'))
                    //->resize($max_width, $max_height, function ($constraint) {
                    ->fit($max_width, $max_height, function ($constraint) { //auto size & crop
                        // $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                $ext = '.jpg';
                $image->encode('jpg', 80); //jpg quality 80

                $hash = sha1($image);

                $newUser->profile_picture = $hash . $ext;
                $newUser->save();

                if (!Storage::disk('avatars')->exists($newUser->profile_picture))
                    Storage::disk('avatars')->put($newUser->profile_picture, $image);
            } catch (Exception $e) {
                //ignore if error
            }
        }


        ToastHelper::addToast('New administrator "' . $newUser->full_name . '" added.', 'Create New Administrator');
        return redirect()->route('admin.user.index');
    }

    public function show(Request $request, User $user)
    {
        //Show user details
        //TODO: Check user permission for selected plant
        dd("TODO: show user", $user);
    }
    public function edit(Request $request, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);
        //Edit User details

        $plants = Plant::get();
        $companies = Company::get();
        foreach ($plants as $p) {
            $regionName = $p->region->name ?? '-';
            if (!isset($regionPlants[$regionName]))
                $regionPlants[$regionName] = [];

            $regionPlants[$regionName][] = $p;
        }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'regionPlants' => $regionPlants,
            'companies' => $companies,
            'user' => $user,
            'currentUser' => User::getCurrent(),
            'plant_user' => $user->plants()->where('plant_user.role', User::PLANT_ROLE_ADMIN)->pluck('plant_user.plant_id'),
        ];
        return view('pages.web.admin.user.edit', $viewData);
    }
    public function update(Request $request, User $user)
    {

        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);

        //Update user
        //Simple store db atm

        $validationRules =
            [
                'staff_no' => [
                    'required',
                    Rule::unique(User::TABLE_NAME, 'staff_no')->ignore($user->id)
                ],
                'full_name' => [
                    'required'
                ],
                'sap_id' => [
                    'required'
                ],
                'email' => [
                    'required',
                    'email',

                ],
                'password' => [
                    'nullable',
                    'confirmed',
                    Rules\Password::defaults()
                ],
                'company_id' => [
                    'nullable',
                    'exists:App\Models\Company,id'
                ]
            ]; //TODO: more field; Permission

        $request->validate($validationRules);
        $data = $request->only(array_keys($validationRules));
        unset($data['password']);
        $user->update($data);
        //temp fill

        $user->role = $request->input('role');
        $user->save();

        $user->plants()->detach();
        if ($request->input('plant-access')) {
            if ($request->input('role') == 1 && count($request->input('plant-access')) > 0) {
                foreach ($request->input('plant-access') as $p) {
                    $user->plants()->attach($p, ['user_id' => $user->id, 'web_permission' => 1, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_ADMIN, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        }

        if ($request->input('designation') && $request->input('designation') != '-') {
            $user->designation = $request->input('designation');
            $user->save();
        }

        if (!is_null($request->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        ToastHelper::addToast('Administrator "' . $user->full_name . '" updated.', 'Update Administrator');
        return redirect()->route('admin.user.index');
    }

    public function destroy(Request $request, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);

        if (!$user->isDestroyable()) {
            ToastHelper::addToast('Unable to delete ' . $user->name . '.', 'Delete User', 'danger');
            return redirect()->route('admin.user.index');
        }


        //Delete user
        //TODO: Guard against accidental delete
        $user->forceDelete();
        ToastHelper::addToast($user->name . ' deleted.', 'Delete User', 'danger');

        return redirect()->route('admin.user.index');
    }

    public function updatePhoto(Request $request, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);


        $request->validate([
            'file' => [
                'image', 'required', File::types(['jpg', 'png'])->max(5 * 1024) //TODO: validation rules for asset attachment
            ]
        ]);

        $max_width = 480;
        $max_height = 480;

        try {
            $image = Image::make($request->file('file'))
                //->resize($max_width, $max_height, function ($constraint) {
                ->fit($max_width, $max_height, function ($constraint) { //auto size & crop
                    // $constraint->aspectRatio();
                    $constraint->upsize();
                });
        } catch (Exception $e) {
            abort('400');
        }

        $ext = '.jpg';
        $image->encode('jpg', 80); //jpg quality 80

        $hash = sha1($image);

        if (!is_null($user->profile_picture)) {

            if (!User::where('profile_picture', '=', $hash . $ext)->whereNot('id', $user->id)->first())
                Storage::disk('avatars')->delete($user->profile_picture); //delete old profile image
        }
        $user->profile_picture = $hash . $ext;
        $user->save();

        if (!Storage::disk('avatars')->exists($user->profile_picture))
            Storage::disk('avatars')->put($user->profile_picture, $image);

        $user->profile_picture_url = $user->getProfilePictureUrl();
        return $user;
    }

    public function deletePhoto(Request $request, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);

        if (!is_null($user->profile_picture)) {
            if (!User::where('profile_picture', '=', $user->profile_picture)->where('id', $user->id)->first())
                Storage::disk('avatars')->delete($user->profile_picture); //delete old profile image
            $user->profile_picture = null;
            $user->save();
        }
        $user->profile_picture_url = $user->getProfilePictureUrl();
        return $user;
    }

    public function datatable(Request $request)
    {
        $dataset = new UserDataset();

        return $dataset
            // ->setFilters('min_role', User::ROLE_PLANT_ADMIN)
            ->setFilters('except_current_user', User::getCurrent()->id)
            ->datatable($request);
    }
}
