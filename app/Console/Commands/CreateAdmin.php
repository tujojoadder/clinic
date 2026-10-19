<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
class CreateAdmin extends Command {
    protected $signature = 'clinic:admin';
    protected $description = 'Create the first clinic administrator with a private password prompt';
    public function handle(): int {
        $name = $this->ask('Administrator name');
        $email = strtolower((string)$this->ask('Administrator email'));
        $password = (string)$this->secret('Password (minimum 12 characters)');
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            $this->error('Valid name, email and 12+ character password required.');
            return self::FAILURE;
        }
        if (User::where('email', $email)->exists()) {
            $this->error('This email already exists. Use clinic:password to reset it.');
            return self::FAILURE;
        }
        User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'admin', 'active' => 1]);
        $this->info('Administrator created. No default credentials were installed.');
        return self::SUCCESS;
    }
}
