<?php

use App\Models\Role;

if(!function_exists('getRoles')){
  function getRoles($role_id = null){
    // return cache()->remember('location_'.$role_id, 60, function() use($role_id){
      if($role_id){
          return Role::where('id', $role_id)->pluck('name', 'id');
        }
      else{
        $user = auth()->user();
        if(!$user->isSuperAdmin()){
          if(!$user->isTeacher()){
            return Role::whereNull('id')->pluck('name', 'id');
          }
          else{
            return Role::whereNotNull('id')->where('name', config('access.role.student'))->pluck('name', 'id');
          }
        }
        else{
          return Role::whereNotNull('id')->whereIn('name', [config('access.role.admin'), config('access.role.teacher')])->pluck('name', 'id');
        }
      }
  // });
  }
}

if(!function_exists('getRole')){
  function getRole($role_id){
    // return cache()->remember('location_'.$role_id, 60, function() use($role_id){
      if($role_id){
        return Role::where('id', $role_id)->first();
      }
    //});
  }
}