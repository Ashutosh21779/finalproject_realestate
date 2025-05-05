<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPropertyView extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    /**
     * Get the property that was viewed.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    
    /**
     * Get the user who viewed the property.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
