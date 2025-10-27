<?php

namespace App\Helpers;

use App\Models\Comment;
use App\Models\Donation;
use App\Models\File;
use App\Models\News;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\Project;
use App\Models\SustainableDevelopmentGoals;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class IconHelper
{
    public static function viewIcon(Model $model, string $size = 'lg')
    {
        ! in_array($size, ['xs', 'sm', 'lg']) ? $size = 'lg' : null;

        return match (get_class($model)) {
            Tenant::class => self::tenantIcon(size: $size),
            User::class => self::usersIcon(size: $size),
            Project::class => self::projectIcon(size: $size),
            SustainableDevelopmentGoals::class => self::sustainableDevelopmentGoalsIcon(size: $size),
            File::class => self::filesIcon(size: $size),
            Comment::class => self::commentsIcon(size: $size),
            Organization::class => self::organizationsIcon(size: $size),
            Donation::class => self::donationsIcon(size: $size),
            News::class => self::newsIcon(size: $size),
            Partner::class => self::partnersIcon(size: $size),
            default => self::activitiesIcon(size: $size)
        };
    }

    public static $sizes = [
        'xs' => ['h' => '2.5', 'w' => '2.5', 'hdiv' => '18px', 'wdiv' => '18px'],
        'sm' => ['h' => '4', 'w' => '4', 'hdiv' => '24px', 'wdiv' => '24px'],
        'lg' => ['h' => '6', 'w' => '6', 'hdiv' => '35px', 'wdiv' => '35px'],
    ];

    public static function donationsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#006494] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                      <path d="M12 7.5a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                      <path fill-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 14.625v-9.75zM8.25 9.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM18.75 9a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V9.75a.75.75 0 00-.75-.75h-.008zM4.5 9.75A.75.75 0 015.25 9h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V9.75z" clip-rule="evenodd" />
                      <path d="M2.25 18a.75.75 0 000 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 00-.75-.75H2.25z" />
                </svg>
            </div>
        blade;
    }

    public static function transactionsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#006494] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                  <path fill-rule="evenodd" d="M15.97 2.47a.75.75 0 011.06 0l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06l3.22-3.22H7.5a.75.75 0 010-1.5h11.69l-3.22-3.22a.75.75 0 010-1.06zm-7.94 9a.75.75 0 010 1.06l-3.22 3.22H16.5a.75.75 0 010 1.5H4.81l3.22 3.22a.75.75 0 11-1.06 1.06l-4.5-4.5a.75.75 0 010-1.06l4.5-4.5a.75.75 0 011.06 0z" clip-rule="evenodd" />
                </svg>
            </div>
        blade;
    }

    public static function sustainableDevelopmentGoalsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#3bafdb] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                </svg>
            </div>
        blade;
    }

    public static function projectIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#005F73] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path d="M19.906 9c.382 0 .749.057 1.094.162V9a3 3 0 00-3-3h-3.879a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H6a3 3 0 00-3 3v3.162A3.756 3.756 0 014.094 9h15.812zM4.094 10.5a2.25 2.25 0 00-2.227 2.568l.857 6A2.25 2.25 0 004.951 21H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-2.227-2.568H4.094z" />
                </svg>
            </div>
        blade;
    }

    public static function tenantIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#fdb713fc] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                      <path d="M21 6.375c0 2.692-4.03 4.875-9 4.875S3 9.067 3 6.375 7.03 1.5 12 1.5s9 2.183 9 4.875z" />
                      <path d="M12 12.75c2.685 0 5.19-.586 7.078-1.609a8.283 8.283 0 001.897-1.384c.016.121.025.244.025.368C21 12.817 16.97 15 12 15s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.285 8.285 0 001.897 1.384C6.809 12.164 9.315 12.75 12 12.75z" />
                      <path d="M12 16.5c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 001.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 001.897 1.384C6.809 15.914 9.315 16.5 12 16.5z" />
                      <path d="M12 20.25c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 001.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 001.897 1.384C6.809 19.664 9.315 20.25 12 20.25z" />
                </svg>
            </div>
        blade;
    }

    public static function usersIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#ff7b00] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                </svg>
            </div>
        blade;
    }

    public static function filesIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-corporate-purple rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        blade;
    }

    public static function commentsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-corporate-salmon-light rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H7.5z" clip-rule="evenodd" />
                </svg>
            </div>
        blade;
    }

    public static function activitiesIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-corporate-blue-street rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                    <path fill-rule="evenodd" d="M3.75 4.5a.75.75 0 01.75-.75h.75c8.284 0 15 6.716 15 15v.75a.75.75 0 01-.75.75h-.75a.75.75 0 01-.75-.75v-.75C18 11.708 12.292 6 5.25 6H4.5a.75.75 0 01-.75-.75V4.5zm0 6.75a.75.75 0 01.75-.75h.75a8.25 8.25 0 018.25 8.25v.75a.75.75 0 01-.75.75H12a.75.75 0 01-.75-.75v-.75a6 6 0 00-6-6H4.5a.75.75 0 01-.75-.75v-.75zm0 7.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" clip-rule="evenodd" />
                </svg>
            </div>
        blade;
    }

    public static function organizationsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#ff7b00] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                      <path fill-rule="evenodd" d="M3 2.25a.75.75 0 000 1.5v16.5h-.75a.75.75 0 000 1.5H15v-18a.75.75 0 000-1.5H3zM6.75 19.5v-2.25a.75.75 0 01.75-.75h3a.75.75 0 01.75.75v2.25a.75.75 0 01-.75.75h-3a.75.75 0 01-.75-.75zM6 6.75A.75.75 0 016.75 6h.75a.75.75 0 010 1.5h-.75A.75.75 0 016 6.75zM6.75 9a.75.75 0 000 1.5h.75a.75.75 0 000-1.5h-.75zM6 12.75a.75.75 0 01.75-.75h.75a.75.75 0 010 1.5h-.75a.75.75 0 01-.75-.75zM10.5 6a.75.75 0 000 1.5h.75a.75.75 0 000-1.5h-.75zm-.75 3.75A.75.75 0 0110.5 9h.75a.75.75 0 010 1.5h-.75a.75.75 0 01-.75-.75zM10.5 12a.75.75 0 000 1.5h.75a.75.75 0 000-1.5h-.75zM16.5 6.75v15h5.25a.75.75 0 000-1.5H21v-12a.75.75 0 000-1.5h-4.5zm1.5 4.5a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75h-.008a.75.75 0 01-.75-.75v-.008zm.75 2.25a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75v-.008a.75.75 0 00-.75-.75h-.008zM18 17.25a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75h-.008a.75.75 0 01-.75-.75v-.008z" clip-rule="evenodd" />
                </svg>
            </div>
        blade;
    }

    public static function newsIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#6a994e] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                  <path fill-rule="evenodd" d="M4.125 3C3.089 3 2.25 3.84 2.25 4.875V18a3 3 0 003 3h15a3 3 0 01-3-3V4.875C17.25 3.839 16.41 3 15.375 3H4.125zM12 9.75a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H12zm-.75-2.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H12a.75.75 0 01-.75-.75zM6 12.75a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5H6zm-.75 3.75a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5H6a.75.75 0 01-.75-.75zM6 6.75a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h3a.75.75 0 00.75-.75v-3A.75.75 0 009 6.75H6z" clip-rule="evenodd" />
                  <path d="M18.75 6.75h1.875c.621 0 1.125.504 1.125 1.125V18a1.5 1.5 0 01-3 0V6.75z" />
                </svg>
            </div>
        blade;
    }

    public static function partnersIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#ff7b00] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                  <path d="M11.584 2.376a.75.75 0 01.832 0l9 6a.75.75 0 11-.832 1.248L12 3.901 3.416 9.624a.75.75 0 01-.832-1.248l9-6z" />
                  <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 010 1.5H3a.75.75 0 010-1.5h.75v-9.918a.75.75 0 01.634-.74A49.109 49.109 0 0112 9c2.59 0 5.134.202 7.616.592a.75.75 0 01.634.74zm-7.5 2.418a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75zm3-.75a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0v-6.75a.75.75 0 01.75-.75zM9 12.75a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75z" clip-rule="evenodd" />
                  <path d="M12 7.875a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" />
                </svg>
            </div>
        blade;
    }

    public static function badgesIcon(string $size = 'lg'): string
    {
        $size = in_array($size, ['xs', 'sm', 'lg']) ? $size : 'lg';

        $sizes = self::$sizes;

        return <<<blade
            <div class="h-[{$sizes[$size]['hdiv']}] w-[{$sizes[$size]['wdiv']}] bg-[#ff7b00] rounded-md flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-{$sizes[$size]['h']} w-{$sizes[$size]['w']} mx-auto my-auto text-white">
                  <path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 0 0-.584.859 6.753 6.753 0 0 0 6.138 5.6 6.73 6.73 0 0 0 2.743 1.346A6.707 6.707 0 0 1 9.279 15H8.54c-1.036 0-1.875.84-1.875 1.875V19.5h-.75a2.25 2.25 0 0 0-2.25 2.25c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-2.25-2.25h-.75v-2.625c0-1.036-.84-1.875-1.875-1.875h-.739a6.706 6.706 0 0 1-1.112-3.173 6.73 6.73 0 0 0 2.743-1.347 6.753 6.753 0 0 0 6.139-5.6.75.75 0 0 0-.585-.858 47.077 47.077 0 0 0-3.07-.543V2.62a.75.75 0 0 0-.658-.744 49.22 49.22 0 0 0-6.093-.377c-2.063 0-4.096.128-6.093.377a.75.75 0 0 0-.657.744Zm0 2.629c0 1.196.312 2.32.857 3.294A5.266 5.266 0 0 1 3.16 5.337a45.6 45.6 0 0 1 2.006-.343v.256Zm13.5 0v-.256c.674.1 1.343.214 2.006.343a5.265 5.265 0 0 1-2.863 3.207 6.72 6.72 0 0 0 .857-3.294Z" clip-rule="evenodd" />
                </svg>
            </div>
        blade;
    }
}
