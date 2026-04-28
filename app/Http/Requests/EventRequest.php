<?php

namespace App\Http\Requests;

use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'capacity' => ['required', 'integer', 'min:1'],
            'has_parking' => ['nullable', 'boolean'],
            'parking_slots' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf(fn () => $this->boolean('has_parking')),
                Rule::prohibitedIf(fn () => ! $this->boolean('has_parking')),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_parking' => $this->boolean('has_parking'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('date') || ! $this->filled('time')) {
                return;
            }

            try {
                $eventDateTime = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    "{$this->input('date')} {$this->input('time')}"
                );
            } catch (\Throwable) {
                return;
            }

            if ($eventDateTime->lt(Carbon::now())) {
                $validator->errors()->add(
                    'time',
                    'Selecciona una fecha y hora que aún no hayan ocurrido.'
                );
            }
        });
    }

    /**
     * @param string|null $key
     * @param mixed $default
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        if ($key !== null) {
            return $validated;
        }

        $validated['has_parking'] = $this->boolean('has_parking');
        $validated['parking_slots'] = $validated['has_parking']
            ? ($validated['parking_slots'] ?? null)
            : null;

        $validated['time'] = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$validated['date']} {$validated['time']}"
        );

        return $validated;
    }
}
