<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_user_id' => 'required|integer|min:1',
            'to_user_id'   => 'required|integer|min:1|different:from_user_id',
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'comment'      => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => is_numeric($this->input('amount')) ? (string) round((float) $this->input('amount'), 2) : $this->input('amount'),
            ]);
        }

        if ($this->has('from_user_id')) {
            $this->merge(['from_user_id' => (int) $this->input('from_user_id')]);
        }
        if ($this->has('to_user_id')) {
            $this->merge(['to_user_id' => (int) $this->input('to_user_id')]);
        }
    }
}
