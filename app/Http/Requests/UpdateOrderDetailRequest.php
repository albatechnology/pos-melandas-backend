<?php

namespace App\Http\Requests;

use App\Models\OrderDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOrderDetailRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_detail_edit');
    }

    public function rules()
    {
        return [];
    }
}
