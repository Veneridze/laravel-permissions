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
            ->publishesServiceProvider('PermissionProvider')
            ->hasMigration('create_roles_table')
            ->hasMigration('add_user_role')
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    //->publishAssets()
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp();
                    //->askToStarRepoOnGitHub();
            });;
            //->hasMigration('create_media_table')
            //->hasViews('media-library')
            //->hasCommands([
            //    RegenerateCommand::class,
            //    ClearCommand::class,
            //    CleanCommand::class,
            //]);
    }

    public function packageBooted(): void
    {
        //$mediaClass = config('media-library.media_model', Media::class);

        //$mediaClass::observe(new MediaObserver);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Permission::class, fn($app) => new Permission());
    }
}
