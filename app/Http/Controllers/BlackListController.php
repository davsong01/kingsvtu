<?php

namespace App\Http\Controllers;

use App\Models\BlackList;
use Illuminate\Http\Request;

class BlackListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bliacklist = BlackList::paginate(paginationRecords());
        $totalBlacklist = $bliacklist->total();
        $activeBlacklist = BlackList::where('status', 'active')->count();
        $inactiveBlacklist = BlackList::where('status', 'in-active')->count();

        return view(themeView('admin', 'customers.blacklist'), [
            'customers' => $bliacklist,
            'totalBlacklist' => $totalBlacklist,
            'activeBlacklist' => $activeBlacklist,
            'inactiveBlacklist' => $inactiveBlacklist,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(themeView('admin', 'customers.create'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'value' => 'required',
            'status' => 'required'
        ]);

        BlackList::create([
            'type' => $request->type,
            'value' => $request->value
        ]);
        return back()->with('message', 'Item added to blacklist successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlackList $blackList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlackList $blackList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlackList $blackList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlackList $blackList)
    {
        //
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $blackList = BlackList::find($request->id);

        if (!$blackList) {
            return response()->json([
                'code' => 0,
                'message' => 'Blacklist entry not found',
            ], 404);
        }

        $status = $blackList->status === 'active' ? 'in-active' : 'active';
        $black = $blackList->update(['status' => $status]);

        if ($black) {
            return response()->json([
                'code' => 1,
                'status' => $status,
                'message' => 'Success',
            ]);
        } else {
            return response()->json([
                'code' => 0,
                'message' => 'Failed to set status',
            ], 500);
        }
    }
}
