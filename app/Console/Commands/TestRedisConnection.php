<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class TestRedisConnection extends Command
{
    protected $signature = 'redis:test';
    protected $description = 'Prueba la conexión a Redis y lista las colas disponibles';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // Verifica la conexión a Redis
            $redis = Redis::connection();
            
            // Realiza una operación simple para verificar la conexión
            $redis->set('test_key', 'test_value');
            $value = $redis->get('test_key');

            // Lista todas las colas disponibles
            $queues = $this->listQueues($redis);

            // Registrar información en el log
            Log::info('Redis connection is working', ['value' => $value, 'queues' => $queues]);

            // Mensaje de éxito en la consola
            $this->info('Redis connection is working, test value retrieved: ' . $value);
            $this->info('Available queues: ' . implode(', ', $queues));
        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error('Error connecting to Redis', ['error' => $e->getMessage()]);
            
            // Mensaje de error en la consola
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Lista las colas disponibles en Redis.
     *
     * @param  \Illuminate\Redis\Connections\Connection  $redis
     * @return array
     */
    protected function listQueues($redis)
    {
        // Redis usa listas para almacenar trabajos en cola. Generalmente, las colas están
        // precedidas por el prefijo de la conexión, por ejemplo, "queues:default".
        // Puedes ajustar el patrón según el prefijo que uses.
        $pattern = 'APILaravel_database_queues:*'; // El patrón de búsqueda para las claves de cola.
        $keys = $redis->keys($pattern);

        return array_map(function ($key) {
            return str_replace('queues:', '', $key);
        }, $keys);
    }
}
