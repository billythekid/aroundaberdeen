<?php

  namespace App\Http\Controllers;

  use App\Map;
  use App\Site;
  use Illuminate\Http\Request;
  use Illuminate\Http\Response;
  use Illuminate\Support\Facades\Validator;

  class SiteController extends Controller
  {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index() {
      $this->authorize('view', Site::class);

      return view('site.index')
        ->withSites(
          Site::where('user_id', '=', auth()->user()->id)
            ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
      $this->authorize('create', Site::class);

      return redirect()->route('site.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request) {
      $validator = Validator::make(
        $request->all(),
        [
          'name' => 'required|unique:sites|min:3|max:23',
        ],
        [
          'name.unique' => 'This name is not available.',
        ]
      );

      $subdomain = str_slug($request->name);

      $validator->after(function ($validator) use ($subdomain) {
        if (Site::where('subdomain', '=', $subdomain)->exists()) {
          $validator->errors()->add('name', 'This name is not available.');
        }
      });

      if ($validator->fails()) {
        return redirect()->route('site.index')
          ->withErrors($validator)
          ->withInput();
      }

      $site            = new Site;
      $site->name      = $request->name;
      $site->subdomain = $subdomain;
      $site->user_id   = auth()->user()->id;

      if ($site->save()) {
        $map = Map::create([
          "user_id" => auth()->user()->id,
          "site_id" => Site::findOrFail($subdomain)->id, // no idea why I can't just use $site->id here, ugh. (something to do with primary keys maybe?)
          "lat"     => "57.14459820700167",
          "lng"     => "-2.1058481488738607",
          "zoom"    => 16,
        ]);
        flash("\"{$site->name}\" created successfully.")->success();
      } else {
        flash("There was a problem saving \"{$site->name}\".")->error();
      }

      return redirect()->route('site.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Site $site
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(Site $site) {
      $this->middleware([]);
      return redirect()->route('subdomain.index', ['subdomain' => $site]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Site $site
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Site $site) {
      $this->authorize('update', $site);

      $site->load('map');

      return view('site.edit')
        ->withSite($site);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param Site     $site
     * @return Site
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Site $site) {
      $this->authorize('update', $site);

      $this->validate($request, [
//        'name' => 'sometimes|required|unique:sites|min:3|max:23',
        "map.lat"   => "sometimes|required",
        "map.lng"   => "sometimes|required",
        "map.zoom"  => "sometimes|required|numeric|min:0|max:23",
        "map.route" => "sometimes|required",
      ]);

      $site->name = $request->has('name') ? $request->name : $site->name;

      $site->save();

      if ($request->has('map')) {
        $map        = $site->map;
        $map->lat   = $request->map['lat'] ?? $map->lat;
        $map->lng   = $request->map['lng'] ?? $map->lng;
        $map->zoom  = $request->map['zoom'] ?? $map->zoom;
        $map->route = $request->map['route'] ?? $map->route ?? "{}";
        $map->save();
      }

      return $site->load('map');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Site $site
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Site $site) {
      $this->authorize('delete', $site);

      $name = $site->name;
      $site->delete();

      flash("Site \"{$name}\" deleted.")->success();

      return redirect()->route('site.index');
    }
  }
