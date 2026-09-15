<?php

namespace App\Services\Access;

use App\Models\Permission;
use App\Models\Property;
use App\Models\User;
use App\Support\ActiveBusinessContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

final class BusinessPermissionService
{
    /** @var array<string, bool> */
    private array $decisions = [];

    public function allows(User $user, ActiveBusinessContext $context, string $permissionKey, ?Property $property = null): bool
    {
        $cacheKey = implode(':', [$user->id, $context->membership->id, $permissionKey, $property?->id ?? 'business']);
        if (array_key_exists($cacheKey, $this->decisions)) {
            return $this->decisions[$cacheKey];
        }

        if ($context->membership->user_id !== $user->id || $context->membership->business_id !== $context->business->id) {
            return $this->decisions[$cacheKey] = false;
        }

        if ($property && $property->business_id !== $context->business->id) {
            return $this->decisions[$cacheKey] = false;
        }

        $permission = Permission::query()->where('key', $permissionKey)->where('status', 'active')->first();
        if (! $permission) {
            return $this->decisions[$cacheKey] = false;
        }

        $overrides = $context->membership->permissionOverrides()
            ->where('permission_id', $permission->id)
            ->where('status', 'active')->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(function ($query) use ($property): void {
                $query->whereNull('property_id');
                if ($property) {
                    $query->orWhere('property_id', $property->id);
                }
            })->get();

        if ($overrides->contains(fn ($override) => $override->effect === 'deny')) {
            return $this->decisions[$cacheKey] = false;
        }

        $roleAllows = $user->roleAssignments()
            ->where('business_membership_id', $context->membership->id)
            ->where('status', 'active')->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role', fn ($role) => $role->where('status', 'active')
                ->whereHas('permissions', fn ($permissions) => $permissions
                    ->where('permissions.id', $permission->id)->where('role_permissions.status', 'active')))
            ->exists();

        $allowed = $roleAllows || $overrides->contains(fn ($override) => $override->effect === 'allow');
        if ($allowed && $property && $this->hasPropertyScope($context)) {
            $allowed = $this->assignedPropertyIds($context)->contains($property->id);
        }

        return $this->decisions[$cacheKey] = $allowed;
    }

    public function scopeProperties(Builder|Relation $query, ActiveBusinessContext $context): Builder|Relation
    {
        if (! $this->hasPropertyScope($context)) {
            return $query;
        }

        return $query->whereIn('properties.id', $this->assignedPropertyIds($context));
    }

    /** @return array<int, string>|null */
    public function permittedPropertyIds(ActiveBusinessContext $context): ?array
    {
        return $this->hasPropertyScope($context)
            ? $this->assignedPropertyIds($context)->values()->all()
            : null;
    }

    public function hasPropertyScope(ActiveBusinessContext $context): bool
    {
        if ($context->membership->roles()->where('status', 'active')
            ->whereHas('role', fn ($role) => $role->where('system_key', 'business_owner'))->exists()) {
            return false;
        }

        return $context->membership->employee?->propertyAssignments()
            ->where('status', 'active')->where('assignment_status', 'active')
            ->where(fn ($query) => $query->whereNull('starts_on')->orWhere('starts_on', '<=', today()))
            ->where(fn ($query) => $query->whereNull('ends_on')->orWhere('ends_on', '>=', today()))
            ->exists() ?? false;
    }

    private function assignedPropertyIds(ActiveBusinessContext $context)
    {
        return $context->membership->employee->propertyAssignments()
            ->where('status', 'active')->where('assignment_status', 'active')
            ->where(fn ($query) => $query->whereNull('starts_on')->orWhere('starts_on', '<=', today()))
            ->where(fn ($query) => $query->whereNull('ends_on')->orWhere('ends_on', '>=', today()))
            ->pluck('property_id');
    }
}
