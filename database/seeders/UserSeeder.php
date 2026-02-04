<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [ //super admin
                'staff_no' => 'admin',
                'sap_id' => 'admin',
                'role' => User::ROLE_SUPER_ADMIN,
                'full_name' => 'Abang Super Min',
                'email' => 'admin@localhost',
                'password' => Hash::make('TSSB@123'),
            ],
            //iav-rayong
            [ //plant-admin rayong
                'staff_no' => 'iav_rayong_admin',
                'sap_id' => 'iav_rayong_admin',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'Admin Rayong',
                'email' => 'rayong-admin@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-rayong',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 1,
                ]
            ],
            [   //rework iav-rayong
                'staff_no' => 'iav_rayong_rework',
                'sap_id' => 'iav_rayong_rework',
                'role' => User::ROLE_USER,
                'full_name' => 'Rework Rayong',
                'email' => 'rayong-rework@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-rayong',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG,
                ]
            ], [   //operator iav-rayong
                'staff_no' => 'iav_rayong_operator',
                'sap_id' => 'iav_rayong_operator',
                'role' => User::ROLE_USER,
                'full_name' => 'Operator Rayong',
                'email' => 'rayong-operator@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-rayong',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_OPERATION_FLAG
                ]
            ],
            [   //all terminal iav-rayong
                'staff_no' => 'iav_rayong_terminal',
                'sap_id' => 'iav_rayong_terminal',
                'role' => User::ROLE_USER,
                'full_name' => 'terminal Rayong',
                'email' => 'rayong-terminal@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-rayong',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG | User::TERMINAL_PERMISSION_OPERATION_FLAG,
                ]
            ],
            [ //web rayong
                'staff_no' => 'iav_rayong_web',
                'sap_id' => 'iav_rayong_web',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'Admin Rayong',
                'email' => 'rayong-web@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-rayong',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 0,
                ]
            ],
            //iav-ayutthaya
            [ //plant-admin ayutthaya
                'staff_no' => 'iav_ayutthaya_admin',
                'sap_id' => 'iav_ayutthaya_admin',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'Admin ayutthaya',
                'email' => 'ayutthaya-admin@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-ayutthaya',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 1,
                ]
            ],
            [   //rework iav-ayutthaya
                'staff_no' => 'iav_ayutthaya_rework',
                'sap_id' => 'iav_ayutthaya_rework',
                'role' => User::ROLE_USER,
                'full_name' => 'Rework ayutthaya',
                'email' => 'ayutthaya-rework@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-ayutthaya',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG,
                ]
            ], [   //operator iav-ayutthaya
                'staff_no' => 'iav_ayutthaya_operator',
                'sap_id' => 'iav_ayutthaya_operator',
                'role' => User::ROLE_USER,
                'full_name' => 'Operator ayutthaya',
                'email' => 'ayutthaya-operator@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-ayutthaya',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_OPERATION_FLAG
                ]
            ],
            [   //all terminal iav-ayutthaya
                'staff_no' => 'iav_ayutthaya_terminal',
                'sap_id' => 'iav_ayutthaya_terminal',
                'role' => User::ROLE_USER,
                'full_name' => 'terminal ayutthaya',
                'email' => 'ayutthaya-terminal@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-ayutthaya',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG | User::TERMINAL_PERMISSION_OPERATION_FLAG,
                ]
            ],
            [ //web iav-ayutthaya
                'staff_no' => 'iav_ayutthaya_web',
                'sap_id' => 'iav_ayutthaya_web',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'web ayutthaya',
                'email' => 'ayutthaya-web@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'iav-ayutthaya',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 0,
                ]
            ],
            //Fine
            [ //plant-admin ayutthaya
                'staff_no' => 'fine_admin',
                'sap_id' => 'fine_admin',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'Admin fine',
                'email' => 'fine-admin@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'fine-component',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 1,
                ]
            ],
            [   //rework fine-component
                'staff_no' => 'fine_rework',
                'sap_id' => 'fine_rework',
                'role' => User::ROLE_USER,
                'full_name' => 'Rework fine',
                'email' => 'fine-rework@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'fine-component',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG,
                ]
            ], [   //operator fine-component
                'staff_no' => 'fine_operator',
                'sap_id' => 'fine_operator',
                'role' => User::ROLE_USER,
                'full_name' => 'Operator fine',
                'email' => 'fine-operator@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'fine-component',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_OPERATION_FLAG
                ]
            ],
            [   //all terminal fine-component
                'staff_no' => 'fine_terminal',
                'sap_id' => 'fine_terminal',
                'role' => User::ROLE_USER,
                'full_name' => 'terminal fine',
                'email' => 'fine-terminal@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'fine-component',
                    'role' => User::PLANT_ROLE_USER,
                    'web_permission' => 0,
                    'terminal_permission' => 1,
                ],
                'work_center_role' => [ //setting for all work center
                    'terminal_permission' => User::TERMINAL_PERMISSION_REWORK_FLAG | User::TERMINAL_PERMISSION_OPERATION_FLAG,
                ]
            ],
            [ //web fine-component
                'staff_no' => 'fine_web',
                'sap_id' => 'fine_web',
                'role' => User::ROLE_PLANT_ADMIN,
                'full_name' => 'fine web',
                'email' => 'fine-web@localhost',
                'password' => Hash::make('TSSB@123'),
                'plant_role' => [
                    'uid' => 'fine-component',
                    'role' => User::PLANT_ROLE_ADMIN,
                    'web_permission' => 1,
                    'terminal_permission' => 0,
                ]
            ],

        ];

        foreach ($users as $user) {


            $plantRole = $user['plant_role'] ?? null;

            $plant = $plantRole ? Plant::where('uid', '=', $plantRole['uid'])->first() : null;

            $workCenterRole = $user['work_center_role'] ?? null;
            $user['plant_id'] = $plant ? $plant->id : null;
            //save user
            $newUser = new User($user);

            unset($newUser->plant_role);
            unset($newUser->work_center_role);

            $newUser->save();

            //set plant role
            if (!isset($plantRole))
                continue;

            if (!$plant)
                continue;

            unset($plantRole['uid']);


            $newUser->plants()->attach($plant, $plantRole);


            if (!isset($workCenterRole))
                continue;


            $workCenters = $plant->onPlantDb()->workCenters;
            $newUser->setConnection($plant->getPlantConnection());
            /** @var \App\Models\WorkCenter $workCenter */
            foreach ($workCenters as $workCenter) {
                $newUser->workCenters()->attach($workCenter, $workCenterRole);
            }
        }
        /*
        //SUPER ADMIN
        ($superadmin = new User())->saveQuietly();  //save without trigger observer events
            // dd($superadmin);
        //PLANT ADMIN
        ($admin = new User())->saveQuietly();  //save without trigger observer events
        $admin->plants()->attach( 1, ['user_id' => $admin->id, 'web_permission' => 1, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_ADMIN , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($admin = new User([
            'staff_no' => 'iav_ayutthaya_admin',
            'sap_id' => 'iav_ayutthaya_admin',
            'role' => User::ROLE_PLANT_ADMIN,
            'full_name' => 'Admin Ayutthaya',
            'email' => 'ayutthaya-admin@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $admin->plants()->attach(2, ['user_id' => $admin->id, 'web_permission' => 1, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_ADMIN , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($admin = new User([
            'staff_no' => 'fine_admin',
            'sap_id' => 'fine_admin',
            'role' => User::ROLE_PLANT_ADMIN,
            'full_name' => 'Admin Fine',
            'email' => 'fine-admin@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $admin->plants()->attach(3, ['user_id' => $admin->id, 'web_permission' => 1, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_ADMIN , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        //USER WEB
        ($user = new User([
            'staff_no' => 'iav_rayong_web',
            'plant_id' => 1,
            'sap_id' => 'iav_rayong_web',
            'role' => User::ROLE_USER,
            'full_name' => 'Web Rayong',
            'email' => 'web-rayong@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(1, ['user_id' => $user->id, 'web_permission' => 1, 'terminal_permission' => 0, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'iav_ayutthaya_web',
            'plant_id' => 2,
            'sap_id' => 'iav_ayutthaya_web',
            'role' => User::ROLE_USER,
            'full_name' => 'Web Ayutthaya',
            'email' => 'web-ayutthaya@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(2, ['user_id' => $user->id, 'web_permission' => 1, 'terminal_permission' => 0, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'fine_web',
            'plant_id' => 3,
            'sap_id' => 'fine_web',
            'role' => User::ROLE_USER,
            'full_name' => 'Web Fine',
            'email' => 'web-fine@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(3, ['user_id' => $user->id, 'web_permission' => 1, 'terminal_permission' => 0, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        //USER OPERATOR
        ($user = new User([
            'staff_no' => 'iav_rayong_operator',
            'plant_id' => 1,
            'sap_id' => 'iav_rayong_operator',
            'role' => User::ROLE_USER,
            'full_name' => 'Operator Rayong',
            'email' => 'operator-rayong@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(1, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'iav_ayutthaya_operator',
            'plant_id' => 2,
            'sap_id' => 'iav_ayutthaya_operator',
            'role' => User::ROLE_USER,
            'full_name' => 'Operator Ayutthaya',
            'email' => 'operator-ayutthaya@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(2, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'fine_operator',
            'plant_id' => 3,
            'sap_id' => 'fine_operator',
            'role' => User::ROLE_USER,
            'full_name' => 'Operator Fine',
            'email' => 'operator-fine@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(3, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        //USER REWORK
        ($user = new User([
            'staff_no' => 'iav_rayong_rework',
            'plant_id' => 1,
            'sap_id' => 'iav_rayong_rework',
            'role' => User::ROLE_USER,
            'full_name' => 'Rework Rayong',
            'email' => 'rework-rayong@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(1, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'iav_ayutthaya_rework',
            'plant_id' => 2,
            'sap_id' => 'iav_ayutthaya_rework',
            'role' => User::ROLE_USER,
            'full_name' => 'Rework Ayutthaya',
            'email' => 'rework-ayutthaya@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(2, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        ($user = new User([
            'staff_no' => 'fine_rework',
            'plant_id' => 3,
            'sap_id' => 'fine_rework',
            'role' => User::ROLE_USER,
            'full_name' => 'Rework Fine',
            'email' => 'rework-fine@localhost',
            'password' => Hash::make('TSSB@123'),
        ]))->saveQuietly();  //save without trigger observer events
        $user->plants()->attach(3, ['user_id' => $user->id, 'web_permission' => 0, 'terminal_permission' => 1, 'role' => User::PLANT_ROLE_USER , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        */
    }
}
