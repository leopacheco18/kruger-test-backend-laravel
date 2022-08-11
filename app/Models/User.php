<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_role',
        'name',
        'lastname',
        'email',
        'cedula',
        'user',
        'password',
        'birthday',
        'address',
        'phone',
        'isVaccinated',
        'datetime_register'
    ];


    protected $hidden = [
        'password'
    ];
    protected $appends = ['vaccine'];

    public function getVaccineAttribute()
    {
        $vaccine = \DB::table('user_vaccine')
                        ->join('vaccines','vaccines.id_vaccine','=','user_vaccine.id_vaccine')
                        ->select(['dose', 'date', 'name', 'vaccines.id_vaccine'])
                        ->where('id_user', $this->id_user)
                        ->first();

        if(!isset($vaccine)){
            $vaccine = new \stdClass();
            $vaccine->id_vaccine = -1;
            $vaccine->date = '';
            $vaccine->dose = 0;
        }

        return $vaccine;

    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
