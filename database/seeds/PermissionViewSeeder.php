<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission; 
use Spatie\Permission\Models\Role;
use App\User;
use Aucos\Permissionview\Models\PermissionModel; 
use Aucos\Permissionview\Models\PermissionAction; 

class PermissionViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        
        $tableNames = config('permissionview.table_names'); 
        
        $permissionBasicActions = array(
            'index' => 'Übersicht', 
            'show' => 'Details', 
            'create' => 'Erstellen', 
            'update' => 'Bearbeiten', 
            'delete' => 'Löschen', 
            'restore' => 'Wiederherstellen'
        ); 

        foreach($permissionBasicActions as $name => $displayName ){ 

            if(PermissionAction::where('name', $name)->count() === 0){
                PermissionAction::create([
                    'name' => $name,
                    'display_name' =>  $displayName,
                    'permission_default' => true
                ]);
            }
        } 

        // $packageModels = array(
        //     'user' => 'User', 
        //     'role' => 'Role', 
        //     'permission-action' => 'Permission Action',
        //     'permission-model' => 'Permission Model',
        //     'permission' => 'Permission'
        // ); 

        // foreach($packageModels as $modelName => $ModelDisplayName ){

        //     if(PermissionModel::where('name', $modelName)->count() === 0){
        //         PermissionModel::create([
        //             'name' =>  $modelName,
        //             'display_name' =>  $ModelDisplayName
        //         ]);
        //     }

        //     if($modelName == 'permission'){
        //         $permissionBasicActions = array(
        //             'index' => 'Übersicht', 
        //             'update' => 'Bearbeiten'
        //         ); 
        //     } 
        //     foreach($permissionBasicActions as $actionName => $ActionDisplayName ){ 
        //          $permission = $modelName.'-'.$actionName;
        //         if(Permission::where('name', $permission)->count() === 0){
        //             Permission::create(['name' => $permission]);
        //         }
        //     }
        // } 

        if(Role::where('name', 'super-admin')->count() === 0){
            Role::create(['name' => 'super-admin']);
        }

      

        $permissionBasicActions = array('index', 'show', 'create', 'update', 'delete', 'restore');
        $otherAction = '';
        $modelName = array();
        foreach (Permission::orderBy('name', 'asc')->get() as $permission) {
             $otherActionStatus = true;
            foreach ($permissionBasicActions as $permissionBasicAction) {
                if (str_is('*-'.$permissionBasicAction, $permission->name)) {
                     $modelName =  explode('-'.$permissionBasicAction, $permission->name);
                    if (PermissionModel::where('name', $modelName[0])->count() === 0) {
                        PermissionModel::create([
                            'name' =>  $modelName[0],
                            'display_name' =>  $modelName[0]
                        ]);
                    }
                     $otherActionStatus = false;
                }
            }
            if ($otherActionStatus) {
                $otherAction = $permission->name;
            }
            // echo $permission->name.'<br>';
            if ($otherAction != '' && count($modelName)) {
                if (str_is($modelName[0].'-*', $otherAction)) {
                     $actionName =  explode($modelName[0].'-', $otherAction);
                    if (PermissionAction::where('name', $actionName[1])->count() === 0) {
                        PermissionAction::create([
                            'name' => $actionName[1],
                            'display_name' =>  $actionName[1],
                            'permission_default' => false
                        ]);
                    }
                }
            }
        } 

        $configUser = config('permissionview.user'); 
        
        if($configUser['email'] != '' && $configUser['password'] != '' && $configUser['field.name'] != '' && $configUser['field.value'] != ''){ 
            if(config('auth.providers.users.model')::where('email', $configUser['email'])->count() === 0){
                $user = config('auth.providers.users.model')::create([
                    $configUser['field.name'] => $configUser['field.value'], 
                    'email' => $configUser['email'],
                    'password' => bcrypt($configUser['password']),  
                ]);
            }
    
            $user = config('auth.providers.users.model')::where('email', $configUser['email'])->first();
            if(!($user->hasRole('super-admin'))){
                    $user->assignRole('super-admin');
            } 
        } 
    }
}