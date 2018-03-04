<?php

  namespace App\Providers;

  use App\Point;
  use App\Policies\PointPolicy;
  use App\Policies\SitePolicy;
  use App\Site;
  use Illuminate\Support\Facades\Gate;
  use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

  class AuthServiceProvider extends ServiceProvider
  {
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      Site::class  => SitePolicy::class,
      Point::class => PointPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
      $this->registerPolicies();

      //
    }
  }
