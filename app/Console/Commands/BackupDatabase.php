<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class BackupDatabase extends Command {
    protected $signature = 'clinic:backup {path : Absolute path outside public/}';
    protected $description = 'Export MySQL schema and data to a private SQL file';
    public function handle(): int {
        if (config('database.default') !== 'mysql') {
            $this->error('MySQL connection required');
            return self::FAILURE;
        }
        $path = $this->argument('path');
        $parent = realpath(dirname($path));
        if (!str_starts_with($path, '/') || !$parent || str_starts_with($parent, realpath(public_path())) || file_exists($path)) {
            $this->error('Use a new absolute filename in an existing private directory outside public/.');
            return self::FAILURE;
        }
        $file = fopen($path, 'x');
        if (!$file) {
            $this->error('Cannot create file');
            return self::FAILURE;
        }
        chmod($path, 0600);
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');
        try {
            fwrite($file, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n");
            foreach ($pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN) as $table) {
                if (!preg_match('/^[a-z_]+$/', $table)) {
                    throw new \RuntimeException('Unexpected table');
                }
                $create = $pdo->query('SHOW CREATE TABLE `' . $table . '`')->fetch(\PDO::FETCH_NUM);
                fwrite($file, 'DROP TABLE IF EXISTS `' . $table . "`;\n" . $create[1] . ";\n");
                $rows = $pdo->query('SELECT * FROM `' . $table . '`');
                while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                    $values = array_map(fn($value) => $value === null?'NULL':$pdo->quote((string)$value), array_values($row));
                    fwrite($file, 'INSERT INTO `' . $table . '` (`' . implode('`,`', array_keys($row)) . '`) VALUES (' . implode(',', $values) . ");\n");
                }
            }
            fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");
            $pdo->commit();
            fclose($file);
            $this->info('Backup complete');
            return self::SUCCESS;
        }
        catch (\Throwable $exception) {
            $pdo->rollBack();
            fclose($file);
            unlink($path);
            throw $exception;
        }
    }
}
