<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Property;

class ChatMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Define fillable fields explicitly
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'property_id',
        'msg',
        'is_read',
        'created_at',
        'updated_at'
    ];

    // Define casts for proper type conversion
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to the sender user
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    // Relationship to the receiver user
    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    // Alias for sender relationship (for compatibility)
    public function user(){
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    // Relationship to the property (if applicable)
    public function property(){
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
}
