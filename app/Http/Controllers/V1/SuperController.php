<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Traits\Super;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Throwable;

class SuperController extends Controller
{
    use Super;

    public array $withAll = [];
    public array $withCount = [];
    public array $withAggregate = [];

    public array $scopes = [];
    public array $scopeWithValue = [];

    public $model;

    public $resource;
    public $storeRequest;
    public $updateRequest;

    public function __construct($model, $resource, $storeRequest, $updateRequest)
    {
        $this->updateRequest = $updateRequest;
        $this->storeRequest = $storeRequest;
        $this->resource = $resource;
        $this->model = $model;
        $constants = new ReflectionClass($this->model);
        try {
            $permissionSlug = $constants->getConstant('PERMISSION_SLUG');
        } catch (Exception $e) {
            $permissionSlug = NULL;
        }
        if ($permissionSlug) {
//            $this->middleware(['permission:view-' . $this->model::PERMISSION_SLUG])->only(['index', 'show']);
//            $this->middleware('permission:create-' . $this->model::PERMISSION_SLUG)->only(['store',]);
//            $this->middleware('permission:update-' . $this->model::PERMISSION_SLUG)->only(['update', 'changeStatus']);
//            $this->middleware('permission:delete-' . $this->model::PERMISSION_SLUG)->only(['delete']);
        }
    }

    /**
     * @throws AccessDeniedException
     */
    public function index(): JsonResource
    {
        return $this->getIndexCollection();
    }

    /**
     * @throws AccessDeniedException
     */
    public function getAll(): JsonResource
    {
        $model = $this->model::initializer()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        $resource = $this->resource;
        if (property_exists($this, 'listResource')) {
            $resource = $this->listResource;
        }
        return $resource::collection($model->get());
    }


    /**
     * @throws Throwable
     * @throws AccessDeniedException
     */
    public function store()
    {
        $model = new $this->model();
        $request = resolve($this->storeRequest);
        if (method_exists($model, 'mergeRequest')) {
            $request->merge($model->mergeRequest());
        }
        $data = $request->only($model->getFillable());

        if ($model->isFillable('created_by')) {
            $data['created_by'] = auth()->id();
        }
        if ($model->isFillable('updated_by')) {
            $data['updated_by'] = auth()->id();
        }
        try {
            DB::beginTransaction();
            $model = $this->model::create($data);
            if (method_exists(new $this->model(), 'afterCreateProcess')) {
                $model->afterCreateProcess();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->somethingWentWrong($e);
        }

        return $this->getResourceObject($this->resource, $model);
    }

    public function delete(Request $request): JsonResponse
    {
        $this->validate($request, [
            'delete_rows' => ['required', 'array'],
            'delete_rows.*' => ['required', 'exists:' . (new  $this->model())->getTable() . ',id'],
        ]);

        try {
            DB::beginTransaction();
            foreach ((array)$request->input('delete_rows') as $item) {
                $model = $this->model::findOrFail($item);
                if (method_exists(new $this->model(), 'afterDeleteProcess') && $model) {
                    $model->afterDeleteProcess();
                }
                if ($model) {
                    $model->delete();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }

        return $this->success(null, 'Data deleted successfully');
    }


    public function show($id)
    {
        $showInitilizer = property_exists($this, 'showInitialize') ? $this->showInitialize : 'initializeModel';
        $model = $this->model::initializer($showInitilizer)
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query))
            ->findOrFail($id);
        $resource = $this->resource;
        if (property_exists($this, 'detailResource')) {
            $resource = $this->detailResource;
        }
        return $this->getResourceObject($resource, $model);
    }

    public function destroy($id)
    {
        $model = $this->model::findOfFail($id);
        if (method_exists(new $this->model(), 'afterDeleteProcess')) {
            $model->afterDeleteProcess();
        }
        $model->delete();
        return $this->success(null, 'Data deleted successfully');
    }


    /**
     * @throws Throwable
     */
    public function changeStatus($id)
    {
        $model = $this->model::findOrFail($id);
        try {
            DB::beginTransaction();
            if (method_exists(new $this->model(), 'beforeChangeStatusProcess')) {
                $model->beforeChangeStatusProcess();
            }
            if (!$this->checkFillable($model, ['status'])) {
                DB::rollBack();
                throw new Exception('Status column not found in fillable');
            }
            $model->update(['status' => $model->status?->value ?? $model->status === 1 ? 0 : 1]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }

        return $this->resource::make($model);
    }

    /**
     * @throws Throwable
     * @throws AccessDeniedException
     */
    public function update($id)
    {
        $model = new $this->model();
        $request = resolve($this->updateRequest);
        if (method_exists($model, 'mergeRequest')) {
            $request->merge($model->mergeRequest($id));
        }
        $data = $request->only($model->getFillable());

        if ($model->isFillable('updated_by')) {
            $data['updated_by'] = auth()->id();
        }
        $model = $this->model::findOrFail($id);

        try {
            if (method_exists(new $this->model(), 'beforeUpdateProcess')) {
                $model->beforeUpdateProcess($model);
            }
            DB::beginTransaction();
            $model->update($data);
            if (method_exists(new $this->model(), 'afterUpdateProcess')) {
                $model->afterUpdateProcess();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }

        return $this->getResourceObject($this->resource, $model);
    }

}
