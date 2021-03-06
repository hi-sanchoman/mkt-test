<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use League\Glide\Server;
use Auth;
use DB;
use Cache;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, Authenticatable, Authorizable;

    protected $casts = [
        'owner' => 'boolean',
        'manager' => 'boolean',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'position_id',
        'password', 
        'owner',
        'manager',
        'photo_path', 
        'remember_token', 
        'account_id',
        'online',
        'message'
    ];

    public function organization(){
        return $this->hasMany(Organization::class);
    }



    public function audtion(){
        return $this->hasMany(Task::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function realization(){
        return $this->hasMany(Realization::class,'realizator','id');
    }

    public function last_realization(){
        return $this->realization()->where('status','!=','3');
    }


    public static function order(){
        $order = [];
        $store = Store::orderBy('num', 'asc')->get();
        
        $ids = User::where('position_id','3')->pluck('id');
        $realizations = Realization::selectRaw('MAX(id) as id')->whereIn('realizator',$ids)->where('status','!=','3')->where('status','!=','4')->groupBy('realizator')->pluck('id');
        // dd($realizations);

        $assortment = [];

        foreach($realizations as $id){
            foreach($store as $item){
                $assortment[] = [
                    'name' => $item->type,
                    'order_amount' => Report::where('realization_id', $id)->where('assortment_id', $item->id)->value('order_amount'), 
                    'amount' => Report::where('realization_id', $id)->where('assortment_id', $item->id)->get(),
                ];
            }


            // $assortment = [
            //         [
            //             'name' => '?????????????????? ?????????? 500 ????', 
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','2')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','2')->get()
            //         ],
            //         [
            //             'name' => '?????????????????? ?????????? 300 ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','3')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','3')->get()
            //         ],
            //         [
            //             'name' => '????. ?????????? 500 ???? ??????????????????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','4')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','4')->get()
            //         ],
            //         [
            //             'name' => '?????????????????? ?????????? ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','5')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','5')->get()
            //         ],
            //         [
            //             'name' => '???????????????? ??????????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','6')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','6')->get()
            //         ],
            //         [
            //             'name' => '????????????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','7')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','7')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 200 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','8')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','8')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 400 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','9')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','9')->get()
            //         ],
            //         [
            //             'name' => '???????????? ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','10')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','10')->get()
            //         ],
            //         [
            //             'name' => '???????????? 4% ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','11')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','11')->get()
            //         ],
            //         [
            //             'name' => '???????????? 250 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','12')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','12')->get()
            //         ],
            //         [
            //             'name' => '?????????? 2.5% 900 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','13')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','13')->get()
            //         ],
            //         [
            //             'name' => '???????????? 300 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','14')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','14')->get()
            //         ],
            //         [
            //             'name' => '???????????? 500 ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','15')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','15')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 15% 500 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','16')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','16')->get()
            //         ],
            //         [
            //             'name' => '?????????? 2.5% 500 ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','17')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','17')->get()
            //         ],
            //         [
            //             'name' => '?????????? ?????????????? 50%',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','18')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','18')->get()
            //         ],
            //         [
            //             'name' => '?????????? ???????????? ????.',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','19')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','19')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 4% 500 ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','20')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','20')->get()
            //         ],
            //         [
            //             'name' => '???????? ?????? ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','21')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','21')->get()
            //         ],
            //         [
            //             'name' => 'C???????????? ?????????? 500 ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','22')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','22')->get()
            //         ],
            //         [
            //             'name' => '???????? ???????????? ????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','23')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','23')->get()
            //         ],
            //         [
            //             'name' => '???????????? 2,5% ?????? ',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','24')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','24')->get()
            //         ],
            //         [
            //             'name' => '?????????? 2,5% ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','25')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','25')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 20%  ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','26')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','26')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 15% ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','27')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','27')->get()
            //         ],
            //         [
            //             'name' => '?????????????? 4% ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','28')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','28')->get()
            //         ],
            //         [
            //             'name' => '???????????? 4% ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','29')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','29')->get()
            //         ],
            //         [
            //             'name' => '?????????????????? ?????????? ??????',
            //             'order_amount' => Report::where('realization_id',$id)->where('assortment_id','30')->value('order_amount'),
            //             'amount' => Report::where('realization_id',$id)->where('assortment_id','30')->get()
            //         ]
            //     ];

            $order[] = [
                'assortment' => $assortment,
                'realizator' => User::find(Realization::where('id',$id)->value('realizator')),
                'status' => Realization::where('id',$id)->value('status'),
                'updated' => Realization::where('id',$id)->value('updated_at'),
            ];

            // dd($order);

            return $order;
        }
        /*$order = [
            '?????????????????? ?????????? 500 ????' => Report::where('realization_id',$id)
            ?????????????????? ?????????? 300 ????
            ????. ?????????? 500 ???? ??????????????????
            ?????????????????? ?????????? ????
            ???????????????? ??????????
            ????????????
            ?????????????? 200 ????.
            ?????????????? 400 ????.
            ???????????? ????
            ???????????? 4% ????.
            ???????????? 250 ????.
            "?????????? 2.5% 900 ????."
            ???????????? 300 ????.
            ???????????? 500 ????
            ?????????????? 15% 500 ????.
            "?????????? 2.5% 500 ????."
            ?????????? ?????????????? 50%
            ?????????? ???????????? ????.
            ?????????????? 4% 500 ????
            ???????? ?????? ????
            C???????????? ?????????? 500 ????
            ???????? ???????????? ????
        ];*/
        return $order;
    }

    public static function plans(int $id = 0){
        
        if($id == 0) {
            $user_id = Auth::user()->id;
        } else {
            $user_id = $id;
        }
        

        $calls = Action::where('type', 1)->where('user_id', $user_id)->whereMonth('date', date('m'))->get();
        $meets = Action::where('type', 2)->where('user_id', $user_id)->whereMonth('date', date('m'))->get();
        $deals = Deal::where('responsible_id', $user_id)->where('status', 1)->whereMonth('created_at', date('m'))->get();


        $plan1 = Plan::where('user_id',  $user_id)->where('type', 1)->latest()->first();
        $plan2 = Plan::where('user_id',  $user_id)->where('type', 2)->latest()->first();
        $plan3 = Plan::where('user_id',  $user_id)->where('type', 3)->latest()->first();

        $plans = [];

        if($plan1) {
            $plans[] = [
                'id' => 1,
                'name' => '??????-???? ??????????????',
                'plan' =>  $calls->count() . ' ???? ' . $plan1->value,
                'type' => 1,
                'fact' => round($calls->count() / $plan1->value * 100),
            ];
        }
        
        if($plan2) {
            $plans[] = [
                'id' => 2,
                'name' => '??????-???? ????????????',
                'plan' => $meets->count() . ' ???? ' . $plan2->value,
                'type' => 2,
                'fact' => round($meets->count() / $plan2->value * 100),
            ];
        }

        if($plan3) {
            $plans[] = [
                'id' => 3,
                'name' => '??????-???? ????????????',
                'plan' => $deals->count() . ' ???? ' . $plan3->value,
                'type' => 3,
                'fact' => round($deals->count() / $plan3->value * 100),
            ];
        }

        return $plans;
    }

    public static function getTasks() {
        $_tasks_1 = Task::where([
            'user_id' => Auth::user()->id,
        ])->orderBy('deadline', 'asc')->with('user')->with('auditor')->get();
        
        $_tasks_2 = Task::where([
            'auditor_id' => Auth::user()->id,
        ])->orderBy('deadline', 'asc')->with('user')->with('auditor')->get();

        return $_tasks_1->merge($_tasks_2);
    }

    public function magazine(){
        return $this->hasMany(Magazine::class,'realizator');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public static function card(int $user_id)
    {   
        $user = self::select('id', 'first_name','last_name', 'photo_path', 'position_id')->where('id', $user_id)->first();
        if($user) {
            $user->position = Position::find($user->position_id);
            $user->name = $user->last_name . ' ' . $user->first_name;
        }
        return $user;
    }



    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function photoUrl(array $attributes)
    {
        if ($this->photo_path) {
            return URL::to(App::make(Server::class)->fromPath($this->photo_path, $attributes));
        }
    }

    public function isDemoUser()
    {
        return $this->email === 'johndoe@example.com';
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeWhereRole($query, $role)
    {
        switch ($role) {
            case 'user': return $query->where('owner', false);
            case 'owner': return $query->where('owner', true);
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }


}
