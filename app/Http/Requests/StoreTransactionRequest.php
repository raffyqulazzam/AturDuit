<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1|max:999999999.99',
            'description' => 'required|string|max:255|min:3',
            'transaction_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'location' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Akun harus dipilih.',
            'account_id.exists' => 'Akun yang dipilih tidak valid.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'type.required' => 'Tipe transaksi harus dipilih.',
            'type.in' => 'Tipe transaksi harus income atau expense.',
            'amount.required' => 'Jumlah harus diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah minimal adalah 1.',
            'amount.max' => 'Jumlah terlalu besar.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.min' => 'Deskripsi minimal 3 karakter.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi harus diisi.',
            'transaction_date.date' => 'Format tanggal tidak valid.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh di masa depan.'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize input
        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags($this->description)
            ]);
        }

        if ($this->has('location')) {
            $this->merge([
                'location' => strip_tags($this->location)
            ]);
        }

        // Ensure user_id is set
        $this->merge([
            'user_id' => auth()->id()
        ]);
    }
}
