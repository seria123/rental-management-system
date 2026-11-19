<?php

namespace App\Http\Middleware;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePropertyAccess
{
    public function handle(Request $request, Closure $next, string $parameter = 'property'): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthenticated.');
        }

        // Admins have access to everything
        if ($user->hasAnyRole(['admin', 'developer'])) {
            return $next($request);
        }

        $propertyId = null;

        // Try to get property ID from route parameters
        if ($request->route('property')) {
            $property = $request->route('property');
            $propertyId = $property instanceof Property ? $property->id : (int) $property;
        } elseif ($request->route('unit')) {
            $unit = $request->route('unit');
            $unitInstance = $unit instanceof Unit ? $unit : Unit::find($unit);
            if ($unitInstance) {
                $propertyId = $unitInstance->property_id;
            }
        } elseif ($request->route('tenant')) {
            $tenant = $request->route('tenant');
            $tenantInstance = $tenant instanceof Tenant ? $tenant : Tenant::find($tenant);
            if ($tenantInstance && $tenantInstance->unit_id) {
                $unitInstance = Unit::find($tenantInstance->unit_id);
                if ($unitInstance) {
                    $propertyId = $unitInstance->property_id;
                }
            }
        } elseif ($request->input('property_id')) {
            $propertyId = (int) $request->input('property_id');
        }

        if ($propertyId && ! $user->canAccessProperty($propertyId)) {
            abort(403, 'You do not have access to this property.');
        }

        return $next($request);
    }
}
