<?php

namespace App\Providers;

use App\Models\File;
use App\Models\User;
use App\Models\Share;
use App\Models\Folder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('do-admin-stuff', function (User $user) {
            return $user->role->roleName == 'Admin';
        });

        Gate::define('open-folder', function(User $user, Folder $folder = NULL) {
            if($folder == NULL){
                return $user;
            } else {
                return $user->role_id === $folder->user->role_id;
            }
        });

        Gate::define('shared-with', function(User $user, Folder $folder) {
            $share = Share::where('role_id','=',$user->role->id)
                            ->where('folder_id','=',$folder->id)
                            ->get();
            if($share){
                return $user;
            }
        });

        Gate::define('can-download', function (User $user, File $file) {
            return $user->role_id === $file->user->role_id;
        });
    }
}
