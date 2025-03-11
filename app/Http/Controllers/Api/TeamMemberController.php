<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::where('is_active', true)->orderBy('sort_order')->get();

        return TeamMemberResource::collection($members);
    }

    public function show($id)
    {
        $member = TeamMember::where('id', $id)->where('is_active', true)->firstOrFail();

        return new TeamMemberResource($member);
    }
}
