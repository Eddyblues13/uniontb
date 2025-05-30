<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'gender',
        'ssn',
        'occupation',
        'country',
        'city',
        'zip',
        'address',
        'nok_name',
        'nok_email',
        'nok_phone',
        'nok_relationship',
        'nok_address',
        'currency',
        'pin',
        'passport_path',
        'kyc_path',
        'code_one',
        'plain',
        'user_status',
        'email_status',
        'verification_code',
        'user_type',
        'login_id',
        'account_number',
        'verification_expiry',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
