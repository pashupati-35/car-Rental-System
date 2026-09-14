<?php

namespace App\Http\Controllers\Admin\Cms\Team;

use App\DTOs\Filters\TeamFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Team\TeamRequest;
use App\Http\Resources\Cms\Team\TeamResource;
use App\Services\Cms\Team\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(protected TeamService $teamService) {}

    public function index(Request $request)
    {
        $filter = TeamFilterDTO::fromArray($request->all());

        return $this->teamService->paginate($filter);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->teamService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function store(TeamRequest $request)
    {
        if ($this->teamService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'Team member created.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create team member.'], 500);
    }

    public function show($id)
    {
        if ($team = $this->teamService->find($id)) {
            return response()->json(['status' => 'OK', 'team' => new TeamResource($team)], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Team member not found.'], 404);
    }

    public function update(TeamRequest $request, $id)
    {
        $team = $this->teamService->update($id, $request->validated());
        if ($team) {
            return response()->json(['status' => 'OK', 'message' => 'Team member updated.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update team member.'], 500);
    }

    public function destroy($id)
    {
        if ($this->teamService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Team member deleted.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete team member.'], 500);
    }
}
