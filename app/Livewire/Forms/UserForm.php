<?php

namespace App\Livewire\Forms;

use App\Models\Employee;
use App\Models\User;
use App\Services\AccountPasswordSetupService;
use App\Services\PasswordChangeRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required|email|unique:users,email')]
    public $email = '';

    #[Validate('required')]
    public $phone = '';

    public bool $requestPasswordChange = false;

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->requestPasswordChange = false;
    }

    public function store(): User
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                // A random, unusable-to-guess password is replaced through the emailed setup link.
                'password' => Str::random(64),
            ]);

            $user->assignRole('customer');

            app(AccountPasswordSetupService::class)->send($user, 'customer');

            return $user;
        });

        $this->reset();

        return $user;
    }

    public function update(): bool
    {
        abort_unless($this->user instanceof User, 404);

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'phone' => ['required', 'string'],
            'requestPasswordChange' => ['boolean'],
        ]);

        $this->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $requested = (bool) ($validated['requestPasswordChange'] ?? false);

        if ($requested) {
            $requester = auth('employee')->user();
            abort_unless($requester instanceof Employee, 403);

            app(PasswordChangeRequestService::class)->sendFor(
                $this->user,
                'customer',
                $requester,
            );
        }

        $this->reset();

        return $requested;
    }
}
