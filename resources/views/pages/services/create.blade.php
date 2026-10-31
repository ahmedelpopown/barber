<?php

use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('إضافة خدمة')] class extends Component
{
    public string $name = '';

    public array $subServices = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:services,name'],
            'subServices' => ['required', 'array', 'min:1'],
            'subServices.*.name' => ['required', 'string', 'max:255'],
            'subServices.*.duration' => ['required', 'integer', 'min:5'],
            'subServices.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function mount(): void
    {
        $this->subServices = [
            ['name' => '', 'duration' => '', 'price' => ''],
        ];
    }

    public function addSubService(): void
    {
        $this->subServices[] = [
            'name' => '',
            'duration' => '',
            'price' => '',
        ];
    }

    public function removeSubService(int $index): void
    {
        if (! array_key_exists($index, $this->subServices)) {
            return;
        }

        unset($this->subServices[$index]);
        $this->subServices = array_values($this->subServices);

        if ($this->subServices === []) {
            $this->addSubService();
        }
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $service = Service::query()->create([
                'name' => $validated['name'],
            ]);

            foreach ($validated['subServices'] as $subService) {
                $service->subServices()->create([
                    'name' => $subService['name'],
                    'duration' => $subService['duration'],
                    'price' => $subService['price'],
                ]);
            }
        });

        session()->flash('message', 'تمت إضافة الخدمة بنجاح.');

        return $this->redirectRoute('admin.services.index');
    }
};
?>

<form wire:submit="save" class="space-y-6">
    @php($button = 'حفظ الخدمة')
    @include('pages.services.partials.form')
</form>
