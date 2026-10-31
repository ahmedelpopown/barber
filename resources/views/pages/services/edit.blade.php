<?php

use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل الخدمة')] class extends Component
{
    public Service $service;

    public string $name = '';

    public array $subServices = [];

    public function mount(Service $service): void
    {
        $this->service = $service->load('subServices');
        $this->name = $service->name;

        $this->subServices = $service->subServices
            ->map(fn ($subService) => [
                'id' => $subService->id,
                'name' => $subService->name,
                'duration' => $subService->duration,
                'price' => $subService->price,
            ])
            ->values()
            ->all();

        if ($this->subServices === []) {
            $this->addSubService();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->ignore($this->service->getKey()),
            ],
            'subServices' => ['required', 'array', 'min:1'],
            'subServices.*.id' => [
                'nullable',
                'integer',
                Rule::exists('sub_services', 'id')->where(
                    fn ($query) => $query->where('service_id', $this->service->getKey())
                ),
            ],
            'subServices.*.name' => ['required', 'string', 'max:255'],
            'subServices.*.duration' => ['required', 'integer', 'min:5'],
            'subServices.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function addSubService(): void
    {
        $this->subServices[] = [
            'id' => null,
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

    public function update()
    {
        $validated = $this->validate();

        $retainedIds = collect($validated['subServices'])
            ->pluck('id')
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $removedBookedSubServiceExists = $this->service->subServices()
            ->whereHas('bookings')
            ->when(
                $retainedIds !== [],
                fn ($query) => $query->whereNotIn('id', $retainedIds)
            )
            ->exists();

        if ($removedBookedSubServiceExists) {
            $this->addError(
                'subServices',
                'لا يمكن حذف خدمة فرعية مرتبطة بحجوزات سابقة، لأن ذلك سيمسح تفاصيلها من سجل الحجز.'
            );

            return;
        }

        DB::transaction(function () use ($validated): void {
            $this->service->update([
                'name' => $validated['name'],
            ]);

            $retainedIds = [];

            foreach ($validated['subServices'] as $data) {
                $attributes = [
                    'name' => $data['name'],
                    'duration' => $data['duration'],
                    'price' => $data['price'],
                ];

                if (! empty($data['id'])) {
                    $subService = $this->service
                        ->subServices()
                        ->whereKey((int) $data['id'])
                        ->firstOrFail();

                    $subService->update($attributes);
                } else {
                    $subService = $this->service->subServices()->create($attributes);
                }

                $retainedIds[] = $subService->getKey();
            }

            $this->service->subServices()
                ->whereNotIn('id', $retainedIds)
                ->delete();
        });

        session()->flash('message', 'تم تعديل الخدمة بنجاح.');

        return $this->redirectRoute('admin.services.index');
    }
};
?>

<form wire:submit="update" class="space-y-6">
    @php($button = 'حفظ التعديلات')
    @include('pages.services.partials.form')
</form>
