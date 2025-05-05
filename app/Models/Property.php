<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function type(){
        return $this->belongsTo(PropertyType::class,'ptype_id','id');
    }

     public function user(){
        return $this->belongsTo(User::class,'agent_id','id');
    }

    public function pstate(){
        return $this->belongsTo(State::class,'state','id');
    }

    // Get state name - works with both state IDs and state names
    public function getStateNameAttribute() {
        // If pstate relationship exists, use it
        if ($this->pstate) {
            return $this->pstate->state_name;
        }

        // Otherwise, check if state column contains a state name directly
        if ($this->state && is_string($this->state) && !is_numeric($this->state)) {
            return $this->state;
        }

        // Default fallback
        return 'N/A';
    }




}
