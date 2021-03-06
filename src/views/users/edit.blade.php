@extends('Permissionview::layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div >
                
                    <ol class="breadcrumb">
                        <li><a href="{!! route('user.index') !!}">User</a></li> 
                        <li><b>User edit </b></li>
                    </ol>
                </div>

                    {!! Form::model($user, ['method' => 'PATCH', 'route' => ['user.update', $user->id]]) !!}

                        @include ('Permissionview::users.form') 

                        <div class="form-group {!! ($errors->has('email')) ? 'has-error' : '' !!}">
                            {!! Form::label('email', 'Email') !!}
                            {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'attendance', 'readonly')) }}
                          
                        </div>

                        <div class="form-group {!! ($errors->has('confirm_password')) ? 'has-error' : '' !!}"> 
                            {!! Form::label('roles', 'Roles') !!}
                            <select class="js-example-basic-multiple role" id="role" value="admin" style="width:100%" name="roles[]" multiple="multiple">
                            @foreach($roles as $role) 
                                @if($user->hasRole($role->name))
                                    <option selected='selected' value="{{ $role->name }}">{{ $role->name }}</option>
                           
                                @else
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endif
                            @endforeach 

                            </select> 
                        </div>

                       
                        <table class="table table-striped table-header-rotated">
                            <thead> 
                                <tr> 
                                    <th></th>
                                    @foreach($permissionActions as $action)
                                    <th class="text-center" ><div class="rotate-45"><span>{!! $action['display_name'] !!}</span></div></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionModel as $model)
                                <tr>
                                    <td>{!! $model['display_name'] !!}</td>
                                    @foreach($permissionActions as $action)
                                    <td class="{!! $model['name'].'-'.$action['name']  !!} text-center " >
                                    @if($permission->where('name', $model['name'].'-'.$action['name'])->count()) 
                                            @if($user->hasDirectPermission($model['name'].'-'.$action['name']))
                                            {!! Form::checkbox($model['name'].'-'.$action['name'] ,1  , true) !!}
                                            @else
                                            {!! Form::checkbox($model['name'].'-'.$action['name']), '' !!}
                                            @endif
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>   

                        {!! Form::submit('update', array('class' => 'btn btn-primary')) !!}

                    {!! Form::close() !!} 

                    
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        var roles = {!! $roles !!};
        rolePermission($('.role').val());
        $('.role').change( function(){ 
            $( "td" ).removeClass( "bg-info" )
            rolePermission($(this).val());
        });

        function rolePermission(selectedRoles){
            roles.filter(function(role) {
                return selectedRoles.indexOf(role.name) > -1;
            }).forEach(function(selectedRole) {
                selectedRole.permissions.forEach(function(permission) {
                    $("."+permission.name).addClass( "bg-info" );
                });
            });
        }
    }); 
   //  $('.email').trigger('change');

 
</script>
@endsection