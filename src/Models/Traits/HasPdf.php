<?php

namespace Wame\LaravelSpatiePdf\Models\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

trait HasPdf
{
    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function addPdf(string $view, string $collection, string $fileName, array $data = [])
    {
        $tempFilename = mb_strtolower(Str::ulid()) . '.pdf';

        $pdf = Pdf::loadView($view, $data)
            ->stream($tempFilename);

        $pdfContent = $pdf->getContent();

        $tempPath = tempnam(sys_get_temp_dir(), 'doc');

        file_put_contents($tempPath, $pdfContent);

        $pdf = new UploadedFile(
            $tempPath,
            $fileName . '.pdf',
            'application/pdf',
            null,
        );

        return $this->addMedia($pdf)->toMediaCollection($collection);
    }
}
