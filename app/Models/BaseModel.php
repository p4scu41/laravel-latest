<?php

namespace App\Models;

use App\Traits\ModelValidation;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use ModelValidation;
}
