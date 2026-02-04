<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property string $name string
 * @property string $capacity integer
 * 
 * @property string $preview_image string
 * 
 * @property string $layout_data medium text
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class DashboardLayout extends Model
{
    const TABLE_NAME = 'dashboard_layouts';
    protected $table = self::TABLE_NAME; 

    //utils
    public function getView(): string | null
    {
        $layoutData = json_decode($this->layout_data);

        if (!$layoutData || !isset($layoutData->view) || !is_string($layoutData->view))
            return null;

        return 'pages.dashboard.' . $layoutData->view;
    }
    //relationships

    //hasmany work_centers
    public function workCenters()
    {
        return $this->hasMany(WorkCenter::class, 'dashboard_layout_id', 'id');
    }
}
