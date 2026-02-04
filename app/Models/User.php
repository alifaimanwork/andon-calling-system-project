<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use App\Extras\Utils\ModelUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $company_id Foreign Key (company): unsigned integer
 * 
 * @property string $staff_no string
 * @property string $sap_id string
 * @property int $role tinyinteger (0 : super admin, 1 : plant admin, 2 : user)
 * @property string $full_name string
 * @property string $email string
 * @property int $enabled tinyinteger (0: Disabled, 1: Enabled)
 * @property string $designation string
 * @property string $profile_picture string
 * @property string $password string
 * @property string $remember_token string
 * 
 * @property string $email_verified_at timestamp
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 */

class User extends Authenticatable implements ModelDestroyable
{
    use Notifiable, HasFactory;

    const TABLE_NAME = 'users';
    protected $table = self::TABLE_NAME;

    const TERMINAL_PERMISSION_OPERATION_FLAG = 0x1;
    const TERMINAL_PERMISSION_REWORK_FLAG = 0x2;

    const ROLE_SUPER_ADMIN = 0;
    const ROLE_PLANT_ADMIN = 1;
    const ROLE_USER = 2;

    const PLANT_ROLE_ADMIN = 0;
    const PLANT_ROLE_USER = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'staff_no',
    //     'full_name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //Activity Log Utils
    public function getCompactInfo()
    {
        return [
            'id' => $this->id,
            'staff_no' => $this->staff_no,
            'sap_id' => $this->sap_id,
        ];
    }

    //Utils
    public function syncToAllPlants()
    {
        //TODO: only sync to related plant
        //Sync to all ATM
        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $dst = User::on($plant->getPlantConnection())->find($this->id);
            if (!$dst) {
                $dst = new User();
                $dst->connection = $plant->getPlantConnection();
            }
            ModelUtils::copyFields($this, $dst);
            $dst->save();
        }
    }
    public function deleteFromAllPlants()
    {
        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            User::on($plant->getPlantConnection())->where('id', $this->id)->delete();
        }
    }

    public static function getCurrent(): User | null
    {
        return Auth::user();
    }

    public function getAccessiblePlants()
    {
        $results = [];
        if ($this->role == User::ROLE_SUPER_ADMIN)
            $results = Plant::get();
        else
            $results = $this->plants()->where(function ($q) {
                $q->where('plant_user.role', '=', User::PLANT_ROLE_ADMIN)->orWhere('plant_user.web_permission', '=', 1);
            })->get();

        return $results;
    }

    public function getProfilePictureUrl($autoNoAvatar = true)
    {
        if (!$this->profile_picture)
            return $autoNoAvatar ? url('/images/default-profile.svg') : null;


        //return Storage::disk('avatars')->url($this->profile_picture);
        return url('/images/avatars/' . $this->profile_picture);
    }

    public function getAdminPlants()
    {
        $results = [];
        if ($this->role == User::ROLE_SUPER_ADMIN)
            $results = Plant::get();
        else
            $results = $this->plants()->where(function ($q) {
                $q->where('plant_user.role', '=', User::PLANT_ROLE_ADMIN);
            })->get();

        return $results;
    }

    public function isSuperAdmin()
    {
        return $this->role == User::ROLE_SUPER_ADMIN;
    }

    public function isPlantAdmin()
    {
        return $this->role <= User::ROLE_PLANT_ADMIN;
    }

    public function isWebAccessible($plant_uid)
    {
        $plant_id = Plant::where('uid', $plant_uid)->first()->id;

        if ($this->role >= User::ROLE_PLANT_ADMIN) {
            return $this->plants()->where('plant_id', $plant_id)->where('web_permission', 1)->exists();
        }
        return false;
    }

    public function isTerminalAccessible($plant_uid, $work_center_uid)
    {
        if ($this->role <= User::ROLE_PLANT_ADMIN)
            return true;

        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        $plant_id = $plant->id;

        if ($this->role == User::ROLE_PLANT_ADMIN) {
            return $this->plants()->where('plant_id', $plant_id)->where('terminal_permission', 1)->exists();
        }
        $workCenters = $plant->onPlantDb()->workCenters()->get()->where('uid', $work_center_uid)->first() ?? null;

        if (!$workCenters)
            return false;

        if ($this->role > User::ROLE_PLANT_ADMIN) {
            $result = DB::connection($plantConnection)
                ->table('user_work_center')
                ->where('user_id', '=', $this->id)
                ->where('work_center_id', '=', $workCenters->id)
                ->where('terminal_permission', '!=', 0)
                ->exists();
            return $result;
        }

        return false;
    }

    public function isTerminalOperator($plant_uid, $work_center_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        if ($this->role <= User::ROLE_PLANT_ADMIN)
            return true;

        $workCenters = $plant->onPlantDb()->workCenters()->get()->where('uid', $work_center_uid)->firstOrFail();

        if ($this->role > User::ROLE_PLANT_ADMIN) {
            $result = DB::connection($plantConnection)
                ->table('user_work_center')
                ->where('user_id', '=', $this->id)
                ->where('work_center_id', '=', $workCenters->id)
                ->where(DB::raw('terminal_permission & ' . self::TERMINAL_PERMISSION_OPERATION_FLAG), '=', self::TERMINAL_PERMISSION_OPERATION_FLAG)
                ->exists();

            return $result;
        }
        return false;
    }

    public function isTerminalRework($plant_uid, $work_center_uid)
    {
        $plant = Plant::where('uid', '=', $plant_uid)->firstOrFail();
        $plant->loadAppDatabase();
        $plantConnection = $plant->getPlantConnection();

        if ($this->role <= User::ROLE_PLANT_ADMIN)
            return true;

        $workCenters = $plant->onPlantDb()->workCenters()->get()->where('uid', $work_center_uid)->firstOrFail();

        if ($this->role > User::ROLE_PLANT_ADMIN) {
            $result = DB::connection($plantConnection)
                ->table('user_work_center')
                ->where('user_id', '=', $this->id)
                ->where('work_center_id', '=', $workCenters->id)
                ->where(DB::raw('terminal_permission & ' . self::TERMINAL_PERMISSION_REWORK_FLAG), '=', self::TERMINAL_PERMISSION_REWORK_FLAG)
                ->exists();

            return $result;
        }
        return false;
    }
    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        return false;
    }

    //relationships

    //belongstomany plants
    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'plant_user', 'user_id', 'plant_id')->withPivot(['web_permission', 'terminal_permission', 'role']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    //hasmany productions
    public function productions()
    {
        return $this->hasMany(Production::class, 'user_id', 'id');
    }

    //hasmany pendings
    public function pendings()
    {
        return $this->hasMany(Pending::class, 'user_id', 'id');
    }

    //hasmany reworks
    public function reworks()
    {
        return $this->hasMany(Rework::class, 'user_id', 'id');
    }

    //hasmany rejects
    public function rejects()
    {
        return $this->hasMany(Reject::class, 'user_id', 'id');
    }

    //hasmany downtimeevents
    public function downtimeEvents()
    {
        return $this->hasMany(DowntimeEvent::class, 'user_id', 'id');
    }

    //belongtomany WorkCenter
    public function workCenters()
    {
        return $this->belongsToMany(WorkCenter::class, 'user_work_center', 'user_id', 'work_center_id', 'id', 'id')->withPivot(['terminal_permission']);
    }
}
