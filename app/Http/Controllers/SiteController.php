<?php

  namespace App\Http\Controllers;

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
      $site->save();

      flash("Site \"{$site->name}\" created successfully.")->success();

      return redirect()->route('site.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Site $site
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(Site $site) {
      return redirect()->route('subdomain.index', ['subdomain' => $site]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Site $site
     * @return void
     */
    public function edit(Site $site) {
      $this->authorize('update', $site);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param Site     $site
     * @return void
     */
    public function update(Request $request, Site $site) {
      $this->authorize('update', $site);

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
