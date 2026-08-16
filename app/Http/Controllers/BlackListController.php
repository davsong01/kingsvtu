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
        $emailBlacklist = BlackList::where('type', 'email')->count();
        $phoneBlacklist = BlackList::where('type', 'phone')->count();

        return view(themeView('admin', 'customers.blacklist'), [
            'customers' => $bliacklist,
            'totalBlacklist' => $totalBlacklist,
            'emailBlacklist' => $emailBlacklist,
            'phoneBlacklist' => $phoneBlacklist,
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
        ]);

        BlackList::firstOrCreate([
            'value' => $request->value,
        ], [
            'type' => $request->type,
            'value' => $request->value,
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
        $blackList->delete();

        return back()->with('message', 'Item removed from blacklist successfully');
    }
}
