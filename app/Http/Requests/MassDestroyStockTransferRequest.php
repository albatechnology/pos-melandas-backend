<?php

namespace App\Http\Requests;

use App\Models\StockTransfer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStockTransferRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('stock_transfer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:stock_transfers,id',
        ];
    }
}
