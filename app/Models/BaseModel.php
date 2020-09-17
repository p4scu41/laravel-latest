<?php

namespace App\Models;

use App\Traits\ModelValidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use HasFactory, LogsActivity, ModelValidation;

    protected static $logAttributes = ['*'];

    protected static $ignoreChangedAttributes = ['updated_at'];

    protected static $logAttributesToIgnore = ['created_at', 'updated_at'];

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;
}
