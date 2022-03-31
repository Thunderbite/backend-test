<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\Campaigns\StoreRequest;
use App\Http\Requests\Backstage\Campaigns\UpdateRequest;
use App\Models\Campaign;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CampaignsController extends BackstageController
{
    public function __construct()
    {
        $this->authorizeResource(Campaign::class, 'campaign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('backstage.campaigns.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('backstage.campaigns.create', [
            'campaign' => new Campaign(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $data = $this->validate(request(), [
            'name' => 'required|unique:campaigns|max:255',
            'timezone' => 'required',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        //parse dates from campaign's timezone
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data['start_date'], $data['timezone'])
            ->setTimezone('UTC');
        $data['start_date'] = $startDate;

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data['end_date'], $data['timezone'])
            ->setTimezone('UTC');
        $data['end_date'] = $startDate;

        // Create the campaign
        $campaign = Campaign::create($data);

        // Set message
        session()->flash('success', 'The campaign has been created!');

        // Redirect
        return redirect()->route('backstage.campaigns.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Campaign $campaign)
    {
        return view('backstage.campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Campaign $campaign
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Campaign $campaign)
    {
        // Validation
        $data = $this->validate(request(), [
            'name' => 'required|max:255|unique:campaigns,name,'.$campaign->id,
            'timezone' => 'required',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        //parse dates from campaign's timezone
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data['start_date'], $data['timezone'])->setTimezone('UTC');
        $data['start_date'] = $startDate;

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data['end_date'], $data['timezone'])->setTimezone('UTC');
        $data['end_date'] = $startDate;

        // Update the campaigns data
        $campaign->update($data);

        // Redirect
        session()->flash('success', 'The campaign details have been saved!');

        return redirect()->route('campaigns.edit', $campaign->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Campaign $campaign
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->forceDelete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        session()->flash('success', 'The campaign has been removed!');

        return redirect(route('backstage.campaigns.index'));
    }

    public function use(Campaign $campaign)
    {
        session()->put('activeCampaign', $campaign->id);

        return redirect()->route('backstage.campaigns.index');
    }
}
