<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Team64j\LaravelManager\Http\Requests\SystemInfoRequest;
use Team64j\LaravelManager\Http\Resources\SystemInfoResource;
use PDO;

class SystemInfoController extends Controller
{
    /**
     * @param SystemInfoRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(SystemInfoRequest $request): AnonymousResourceCollection
    {
        $data = [
            [
                'name' => Lang::get('global.modx_version'),
                'data' => Config::get('global.settings_version'),
            ],
            [
                'name' => Lang::get('global.release_date'),
                'data' => '',//$this->managerTheme->getCore()->getVersionData('release_date')
            ],
            [
                'name' => 'PHP Version',
                'data' => phpversion(),
            ],
            [
                'name' => Lang::get('global.access_permissions'),
                'data' => Lang::get(Config::get('global.use_udperms') ? 'enabled' : 'disabled'),
            ],
            [
                'name' => Lang::get('global.servertime'),
                'data' => date('H:i:s', time()),
            ],
            [
                'name' => Lang::get('global.localtime'),
                'data' => date('H:i:s', time() + Config::get('global.server_offset_time')),
            ],
            [
                'name' => Lang::get('global.serveroffset'),
                'data' => Config::get('global.server_offset_time') / (60 * 60) . ' h',
            ],
            [
                'name' => Lang::get('global.database_name'),
                'data' => DB::connection()->getDatabaseName(),
            ],
            [
                'name' => Lang::get('global.database_server'),
                'data' => DB::connection()->getConfig('host'),
            ],
            [
                'name' => Lang::get('global.database_version'),
                'data' => DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
            ],
            [
                'name' => Lang::get('global.database_charset'),
                'data' => $this->resolveCharset(),
            ],
            [
                'name' => Lang::get('global.database_collation'),
                'data' => $this->resolveCollation(),
            ],
            [
                'name' => Lang::get('global.table_prefix'),
                'data' => DB::connection()->getTablePrefix(),
            ],
        ];

        foreach (get_defined_constants() as $key => $value) {
            if (Str::startsWith($key, ['MODX_', 'EVO_'])) {
                $data[] = [
                    'name' => $key,
                    'data' => $value,
                ];
            }
        }

        return SystemInfoResource::collection([
            'data' => [
                'data' => [
                    [
                        'data' => $data,
                    ],
                ],
            ],
        ]);
    }

    protected function resolveCharset()
    {
        switch (Config::get('database.default')) {
            case 'pgsql':
//                $result = $this->database->query("SELECT * FROM pg_settings WHERE name='client_encoding'");
//                $charset = $this->database->getRow($result, 'num');
//
//                return $charset[1];

                return '';

            case 'mysql':
                return DB::selectOne("show variables like 'character_set_database'")->Value;

            default :
                return 'none';
        }
    }

    protected function resolveCollation()
    {
        switch (Config::get('database.default')) {
            case 'pgsql':
//                $result = $this->database->query("SELECT * FROM pg_settings WHERE name = 'lc_collate'");
//                $charset = $this->database->getRow($result, 'num');
//
//                return $charset[1];

                return '';

            case 'mysql':
                return DB::selectOne("show variables like 'collation_database'")->Value;

            default :
                return 'none';
        }
    }
}
