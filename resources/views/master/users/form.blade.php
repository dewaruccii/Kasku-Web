<div class="row">
    <x-textInput name="name" label="Full Name" :mandatory="true" :old="$user->name ?? ''" />
    <x-textInput name="email" label="Email" :mandatory="true" :old="$user->email ?? ''" />
    <x-selectOption name="role" label="Role" :mandatory="true" :data="$role" :old="$user->roles[0]->id ?? ''" />
    <x-textInput name="password" label="Password" type="password" />
</div>
