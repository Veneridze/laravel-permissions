<?php
namespace Veneridze\LaravelPermission;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class PermissionProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-permission')
            ->hasConfigFile()
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp();
            });
    }

    public function packageBooted(): void
    {

    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Permission::class, fn($app): Permission => new Permission());
    }
}
