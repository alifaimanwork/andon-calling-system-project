<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use App\Extras\Utils\ModelUtils;
use Illuminate\Database\Eloquent\Model;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property string $name string
 * @property string $code string
 * @property string $logo string
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class Company extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'companies';
    protected $table = self::TABLE_NAME;

    protected $guarded = [];

    //Utils
    public function syncToAllPlants()
    {

        //TODO: only sync to related plant
        //Sync to all ATM
        $plants = Plant::get();
        foreach ($plants as $plant) {
            $plant->loadAppDatabase();
            $dst = Company::on($plant->getPlantConnection())->find($this->id);
            if (!$dst) {
                $dst = new Company();
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
            Company::on($plant->getPlantConnection())->where('id', $this->id)->delete();
        }
    }

    public function isDestroyable(string &$reason = null): bool
    {
        
        //TODO, only return true when no other resource references to this
        if(!$this->plants()->first())
            return true;
        
        $reason = 'Company has plant';
        return false;
    }


    /* Relationship */

    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function plants()
    {
        return $this->hasMany(Plant::class, 'company_id', 'id');
    }
}
