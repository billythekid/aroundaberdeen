<?php

  namespace App\Http\Controllers;

  use App\Map;
  use App\Point;
  use Illuminate\Http\Request;

  class PointController extends Controller
  {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
      //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
      //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

      $this->validate($request, [
        'map' => 'required|numeric',
      ]);
      $map = Map::findOrFail($request->input('map'));

      $point       = new Point();
      $point->name = "New " . strtolower(str_singular($map->site->name));
      $point->lat  = $map->lat;
      $point->lng  = $map->lng;
      $point->order = 0;

      $map->points()->save($point);

      return response()->json($point);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
      //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Point                     $point
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Point $point) {
      $this->authorize('update', $point);

      $this->validate($request, [
        'name' => 'required',
        'lat'  => 'required|numeric',
        'lng'  => 'required|numeric',
      ]);

      $point->name = $request->input('name');
      $point->lat  = $request->input('lat');
      $point->lng  = $request->input('lng');
      $point->save();

      return response()->json(['success' => true, 'point' => $point]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Point $point
     * @return
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Point $point) {
      $this->authorize('delete', $point);
      $map = $point->map;
      $point->delete();

      return response()->json($map->points);
    }


    public function saveOrder(Request $request) {
      $this->authorize('saveOrder', Point::class);
      $this->validate($request, [
        'points'=>'required|array'
      ]);
      foreach($request->input('points') as $key=>$point)
      {

        $point = Point::findOrFail($point);
        $point->order = $key;
        $point->save();
      }
      return response()->json($point->map->points);
    }
  }
