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
        // Utiliza un patrón más general para buscar las claves de cola
        $iterator = null;
        $queues = [];
        $pattern = 'redis:queues:*';

        do {
            list($iterator, $keys) = $redis->scan($iterator, $pattern);
            foreach ($keys as $key) {
                // Extrae el nombre de la cola eliminando el prefijo 'queues:'
                $queues[] = str_replace('queues:', '', $key);
            }
        } while ($iterator);

        return $queues;
    }

}
