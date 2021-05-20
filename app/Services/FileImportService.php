<?php

namespace App\Services;

use App\Classes\ImportManager;
use App\Contracts\ExceptionMessage;
use App\Enums\Import\ImportBatchStatus;
use App\Enums\Import\ImportBatchType;
use App\Enums\Import\ImportMode;
use App\Jobs\MarkImportBatchAsFullyDispatchedJob;
use App\Jobs\ProcessImportLineJob;
use App\Models\ImportBatch;
use App\Models\ImportLine;
use Exception;
use Illuminate\Support\Facades\Bus;
use Throwable;


class FileImportService
{

    /**
     * @param ImportBatchType $type
     * @param int $company_id
     * @param string $file
     * @return ?ImportBatch
     * @throws Exception
     */
    public static function importFromRequest(
        ImportBatchType $type,
        int $company_id,
        string $file = 'file',
    ): ?ImportBatch
    {
        // Grab file from request
        $file = request()->file($file);
        if (empty($file)) {
            throw new Exception(sprintf(ExceptionMessage::ImportFileMissingFromRequest, $file));
        }

        $import = new ImportManager($file, $type, $company_id);
        $import->preview();

        return $import->getBatch();
    }

    /**
     * @param ImportBatch $importBatch
     * @param ImportMode $mode
     * @throws Throwable
     */
    public static function processImportBatch(ImportBatch $importBatch, ImportMode $mode)
    {
        $importBatch->update(['mode' => $mode]);

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function () use ($importBatch) {
                if (!$importBatch->refresh()->all_jobs_added_to_batch_at) {
                    return;
                }

                $importBatch->refreshSummary();
                $importBatch->update(['status' => ImportBatchStatus::FINISHED]);
            })
            ->dispatch();

        ImportLine::query()
            ->where('import_batch_id', $importBatch->id)
            ->cursor()
            ->map(fn(ImportLine $importLine) => new ProcessImportLineJob($importLine, $importBatch))
            ->filter()
            ->each(fn(ProcessImportLineJob $job) => $batch->add([$job]));

        $batch->add([new MarkImportBatchAsFullyDispatchedJob($importBatch)]);
    }
}
