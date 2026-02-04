<?php

namespace App\Http\Controllers\Web\Settings;

use App\Models\User;
use App\Models\Plant;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Extras\Utils\ToastHelper;
use Illuminate\Support\Facades\DB;
use App\Extras\Datasets\UserDataset;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    //TODO: Resource Guard


    public function index(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();


        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'currentUser' => User::getCurrent(),

        ];
        return view('pages.web.plant-settings.user.index', $viewData);
    }

    public function create(Request $request, $plant_uid)
    {
        //Show create new user page

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $workCenters = $plant->onPlantDb()->workCenters()->get();


        //Plant list group by regions
        $company = $plant->company;
        $regionPlants = [];
        foreach ($company->plants as $p) {
            $regionName = $p->region->name ?? '-';
            if (!isset($regionPlants[$regionName]))
                $regionPlants[$regionName] = [];

            $regionPlants[$regionName][] = $p;
        }

        // if(User::getCurrent()->role == User::ROLE_SUPER_ADMIN) {
        //     $users = User::on($plantConnection)->get();
        // } else {
        //     $users = User::on($plantConnection)->where('id', '!=', User::getCurrent()->id)->get();
        // }

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'regionPlants' => $regionPlants,
            'currentUser' => User::getCurrent(),
            'workCenters' => $workCenters,
            'admin_plants' => User::getCurrent()->getAdminPlants(),
        ];

        return view('pages.web.plant-settings.user.create', $viewData);
    }

    public function store(Request $request, $plant_uid)
    {
        //Store new user
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $error_msg = [];

        //validate input with return error message
        $validated = $request->validate([
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
            'company' => [
                'required'
            ],
            'wc_plant_id' => [
                'required'
            ],
        ]);


        // if (count($validated) > 0) {
        //     return redirect()->back()->withInput()->withErrors($validated);
        // }

        //retun validation error if any



        // if (!$request->has('access-ipos')) {
        //     $error_msg['access-ipos'] = 'Please select at least one platform access';
        // }

        // if ($request->has('access-ipos')) { //if user has access to any platform
        //     $access_ipos = $request->get('access-ipos');
        //     if (in_array('2', $access_ipos)) { // if terminal access is selected
        //         if (!@$request->has('wc-op') && !@$request->has('wc-rework')) { //no work center access selected
        //             $error_msg['access-ipos'] = 'Please select at least one work center access';
        //         }
        //     }
        // }


        if (count($error_msg) > 0) {
            return redirect()->back()->withErrors($error_msg)->withInput();
        } else {
            //access-ipos
            $web_access_dat = 0;
            $terminal_access_dat = 0;
            $plant_role = User::PLANT_ROLE_USER;

            if ($request->has('access-ipos')) {
                $access_ipos = $request->get('access-ipos');

                if (in_array('2', $access_ipos)) { //if terminal access is selected
                    $terminal_access_dat = 1;
                }
                if (in_array('1', $access_ipos)) { //if web access is selected
                    $web_access_dat = 1;
                }
                if (in_array('3', $access_ipos)) { //if both web and terminal access is selected (administrator)
                    $web_access_dat = 1;
                    $terminal_access_dat = 1;
                    $plant_role = User::PLANT_ROLE_ADMIN;
                }
            }

            $user = new User();
            $user->plant_id = $request->get('wc_plant_id');
            $user->full_name = $request->get('full_name');
            $user->staff_no = $request->get('staff_no');
            $user->email = $request->get('email');
            $user->password = bcrypt($request->get('password'));
            $user->sap_id = $request->get('sap_id');
            $user->company_id = $request->get('company');

            $user->enabled = 1;
            $user->role = User::ROLE_USER;
            $user->save();

            $user->plants()->detach();
            $user->plants()->attach($plant->id, ['user_id' => $user->id, 'web_permission' => $web_access_dat, 'terminal_permission' => $terminal_access_dat, 'role' => $plant_role, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);


            $wc_op = $request->get('wc-op'); //list of work centers id
            $wc_rework = $request->get('wc-rework'); //list of work centers id

            $workcenter = []; // id => permission (0: null, 1: operation, 2: rework, 3: both)

            if ($request->has('wc-op')) {
                foreach ($wc_op as $wc_id) {
                    $workcenter[$wc_id] = 1;
                }
            }
            if ($request->has('wc-rework')) {
                foreach ($wc_rework as $wc_id) {
                    if (!isset($workcenter[$wc_id]))
                        $workcenter[$wc_id] = 2;
                    else
                        $workcenter[$wc_id] = 3;
                }
            }

            foreach ($workcenter as $wc_id => $permission) {

                $user->setConnection($plantConnection)->workCenters()->attach($wc_id, ['user_id' => $user->id, 'terminal_permission' => $permission, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }
        return redirect()->route('settings.user.index', $plant_uid);
    }



    public function edit(Request $request, $plant_uid, $viewUser)
    {
        //Show create new user page

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $workCenters = $plant->onPlantDb()->workCenters()->get();


        //Plant list group by regions
        $company = $plant->company;
        $regionPlants = [];
        foreach ($company->plants as $p) {
            $regionName = $p->region->name ?? '-';
            if (!isset($regionPlants[$regionName]))
                $regionPlants[$regionName] = [];

            $regionPlants[$regionName][] = $p;
        }

        $user = User::where('id', '=', $viewUser)->firstOrFail();
        $user->plant_name = Plant::where('id', '=', $user->plant_id)->first()->name ?? '';

        $viewData = [
            'topBarTitle' => 'PLANT SETTINGS',
            'plant' => $plant,
            'select_plant_list' => Plant::where('region_id', '=', $plant->region_id)->get(),
            'regionPlants' => $regionPlants,
            'currentUser' => User::getCurrent(),
            'workCenters' => $workCenters,
            'user_data' => $user,
            'admin_plants' => User::getCurrent()->getAdminPlants(),
            'web_platform_access' => $user->plants()->where('plant_id', '=', $plant->id)->first()->pivot->web_permission ?? 0,
            'terminal_platform_access' => $user->plants()->where('plant_id', '=', $plant->id)->first()->pivot->terminal_permission ?? 0,
            'role_access' => $user->plants()->where('plant_id', '=', $plant->id)->first()->pivot->role ?? User::PLANT_ROLE_USER,
            'workcenter_access' => DB::connection($plantConnection)->table('user_work_center')->where('user_id', '=', $user->id)->get(),
        ];
        return view('pages.web.plant-settings.user.edit', $viewData);
    }

    public function update(Request $request, $plant_uid, $viewUser)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $error_msg = [];
        $admin_plants_array = User::getCurrent()->getAdminPlants();

        $admin_plant_ids = [];
        foreach ($admin_plants_array as $p) {
            $admin_plant_ids[] = $p->id;
        }

        $user = User::where('id', '=', $viewUser)->firstOrFail();

        $auth_edit = false;

        if (!in_array($user->plant_id, $admin_plant_ids)) {
            $auth_edit = false;
        } else {
            $auth_edit = true;

            //validation
            $validated = $request->validate([
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

                'company' => [
                    'required'
                ],
                'wc_plant_id' => [
                    'required'
                ],
            ]);
        }
        if (count($error_msg) > 0) {
            return redirect()->back()->withErrors($error_msg)->withInput();
        } else {
            //access-ipos
            $web_access_dat = 0;
            $terminal_access_dat = 0;
            $plant_role = User::PLANT_ROLE_USER;

            if ($request->has('access-ipos')) {
                $access_ipos = $request->get('access-ipos');

                if (in_array('2', $access_ipos)) { //if terminal access is selected
                    $terminal_access_dat = 1;
                }
                if (in_array('1', $access_ipos)) { //if web access is selected
                    $web_access_dat = 1;
                }
                if (in_array('3', $access_ipos)) { //if both web and terminal access is selected (administrator)
                    $web_access_dat = 1;
                    $terminal_access_dat = 1;
                    $plant_role = User::PLANT_ROLE_ADMIN;
                }
            }

            $old_password = $user->password;

            if ($request->has('password') && $request->get('password') != '') {
                $request->validate([
                    'password' => [
                        'required',
                        'confirmed',
                        Rules\Password::defaults()
                    ],
                ]);
            }
            if ($auth_edit) {
                $user->plant_id = $request->get('wc_plant_id');
                $user->full_name = $request->get('full_name');
                $user->staff_no = $request->get('staff_no');
                $user->email = $request->get('email');
                $user->sap_id = $request->get('sap_id');
                $user->enabled = $request->get('enabled');
                if ($request->has('password') && $request->get('password') != '') {
                    $user->password = Hash::make($request->get('password'));
                }
                $user->save();
            }

            //delete all previous access
            $user->plants()->detach($plant);
            if ($web_access_dat || $terminal_access_dat) {
                $user->plants()->attach($plant->id, ['user_id' => $user->id, 'web_permission' => $web_access_dat, 'terminal_permission' => $terminal_access_dat, 'role' => $plant_role, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            }
            $wc_op = $request->get('wc-op'); //list of work centers id
            $wc_rework = $request->get('wc-rework'); //list of work centers id

            $workcenter = []; // id => permission (0: null, 1: operation, 2: rework, 3: both)

            if ($request->has('wc-op')) {
                foreach ($wc_op as $wc_id) {
                    $workcenter[$wc_id] = 1;
                }
            }
            if ($request->has('wc-rework')) {
                foreach ($wc_rework as $wc_id) {
                    if (!isset($workcenter[$wc_id]))
                        $workcenter[$wc_id] = 2;
                    else
                        $workcenter[$wc_id] = 3;
                }
            }

            DB::connection($plantConnection)->table('user_work_center')->where('user_id', '=', $user->id)->delete();
            foreach ($workcenter as $wc_id => $permission) {
                $user->setConnection($plantConnection)->workCenters()->attach($wc_id, ['user_id' => $user->id, 'terminal_permission' => $permission, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        return redirect()->route('settings.user.index', $plant_uid);
    }
    public function updatePhoto(Request $request, $plant_uid, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

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

    public function deletePhoto(Request $request, $plant_uid, User $user)
    {
        $currentUser = User::getCurrent();
        if ($user->id == $currentUser->id)
            abort(404);

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        
        if (!is_null($user->profile_picture)) {
            if (!User::where('profile_picture', '=', $user->profile_picture)->where('id', $user->id)->first())
                Storage::disk('avatars')->delete($user->profile_picture); //delete old profile image
            $user->profile_picture = null;
            $user->save();
        }
        $user->profile_picture_url = $user->getProfilePictureUrl();
        return $user;
    }
    public function datatable(Request $request, $plant_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();

        $dataset = new UserDataset();

        $plantIds = User::getCurrent()->getAdminPlants()->pluck('id')->toArray();


        $dataset->setFilters('plant_id', $plant->id)
            ->setFilters('role', User::ROLE_USER)
            ->setFilters('except_current_user', User::getCurrent()->id)
            ->setFilters('origin_plant_id', $plantIds);


        return $dataset->datatable($request);
    }
}
