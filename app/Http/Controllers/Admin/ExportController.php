<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Import\ImportBatchType;
use App\Exports\ImportSampleExport;
use App\Http\Controllers\Controller;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * @param string $type
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function sample(string $type)
    {
        $type = ImportBatchType::fromValue((int)$type);

        return Excel::download(new ImportSampleExport($type), 'sample.csv');
    }
}
