<?php
namespace App\Filament\Resources\PostResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use function Filament\authorize;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = PostResource::class;


    public function handler()
    {
        $query = static::getEloquentQuery();
        $model = static::getModel();
        $user = Auth::user();
        Gate::forUser($user)->authorize('viewAny', $model);

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
