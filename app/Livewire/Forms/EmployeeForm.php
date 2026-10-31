<?php

namespace App\Livewire\Forms;

use App\Models\Employee;
use App\Services\AccountPasswordSetupService;
use App\Services\PasswordChangeRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class EmployeeForm extends Form
{
    public ?Employee $employee = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $status = 'active';

    public bool $requestPasswordChange = false;

    protected function rules(): array
    {
        $emailRule = Rule::unique('employees', 'email');

        if ($this->employee?->exists) {
            $emailRule->ignore($this->employee->getKey());
        }

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $emailRule],
            'phone' => ['required', 'string', 'max:30'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'requestPasswordChange' => ['boolean'],
        ];
    }

    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->status = $employee->status;
        $this->requestPasswordChange = false;
    }

    public function store(): Employee
    {
        $validated = $this->validate();

        $employee = DB::transaction(function () use ($validated): Employee {
            $employee = Employee::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
                // The employee chooses their password from the one-time setup link.
                'password' => Str::random(64),
            ]);

            $employee->assignRole('barber');

            app(AccountPasswordSetupService::class)->send($employee, 'employee');

            return $employee;
        });

        $this->reset();

        return $employee;
    }

    public function update(): bool
    {
        abort_unless($this->employee?->exists, 404);

        $validated = $this->validate();

        $this->employee->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ]);

        $requested = (bool) ($validated['requestPasswordChange'] ?? false);

        if ($requested) {
            $requester = auth('employee')->user();
            abort_unless($requester instanceof Employee, 403);

            app(PasswordChangeRequestService::class)->sendFor(
                $this->employee,
                'employee',
                $requester,
            );
        }

        $this->reset();

        return $requested;
    }
}
