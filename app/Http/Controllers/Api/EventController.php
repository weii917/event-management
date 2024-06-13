<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use App\Http\Resources\EventResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{

    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    // 加入驗證檢查排除index ,show
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(Event::class, 'event');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // $this->shouldIncludeRelation('user');
        $query = $this->loadRelationships(Event::query());



        return EventResource::collection($query->latest()->paginate());
    }



    /**  
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // 驗證請求數據
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);
        // dd($request);
        // 合併驗證數據和其他數據
        // 當驗證後會request包含當前認證用戶的資料，就可以使用user() 方法來獲取用戶實例並訪問其屬性
        $data = array_merge($validatedData, [
            'user_id' => $request->user()->id,
        ]);

        // 使用合併後的數據創建事件
        $event = Event::create($data);
        return new EventResource($this->loadRelationships($event));
    }

    /** 
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // 加載關聯數據是根據modal Event裡的function命名
        // $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'You are not authroized to update this event.');
        // }

        // $this->authorize('update-event', $event);

        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time'
            ])
        );
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
