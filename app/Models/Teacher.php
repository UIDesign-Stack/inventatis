<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = ['nip', 'name', 'phone', 'subject'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
