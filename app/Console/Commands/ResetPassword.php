<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
class ResetPassword extends Command {
    protected $signature = 'clinic:password';
    protected $description = 'Reset a staff password from the server console';
    public function handle(): int {
        $user = User::where('email', $this->ask('User email'))->first();
        if (!$user) {
            $this->error('User not found');
            return self::FAILURE;
        }
        $password = (string)$this->secret('New password (minimum 12 characters)');
        if (strlen($password) < 12) {
            $this->error('Password too short');
            return self::FAILURE;
        }
        $user->update(['password' => Hash::make($password), 'remember_token' => null]);
        $this->info('Password updated');
        return self::SUCCESS;
    }
}
