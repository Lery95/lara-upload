<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Events\UploadStatusUpdated;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function handle(): void
    {
        $this->upload->update(['status' => 'processing']);
        // event(new UploadStatusUpdated($this->upload));

        try {
            $file = Storage::get($this->upload->filepath);
            $file = mb_convert_encoding($file, 'UTF-8', 'UTF-8');

            $csv = Reader::createFromString($file);
            $csv->setHeaderOffset(0);
            $records = (new Statement())->process($csv);

            // $file = Storage::readStream($this->upload->filepath); // Use a stream instead
            // $csv = Reader::createFromStream($file);
            // $csv->setHeaderOffset(0);
            // $records = (new Statement())->process($csv);

            foreach ($records as $record) {
                Product::updateOrCreate(
                    ['unique_key' => $record['UNIQUE_KEY']],
                    [
                        'product_title' => $record['PRODUCT_TITLE'],
                        'product_description' => $record['PRODUCT_DESCRIPTION'],
                        'style_number' => $record['STYLE#'],
                        'mainframe_color' => $record['SANMAR_MAINFRAME_COLOR'],
                        'size' => $record['SIZE'],
                        'color_name' => $record['COLOR_NAME'],
                        'piece_price' => $record['PIECE_PRICE'],
                    ]
                );
            }

            $this->upload->update(['status' => 'completed']);
            // event(new UploadStatusUpdated($this->upload));
        } catch (\Exception $e) {
            $this->upload->update(['status' => 'failed']);
            // event(new UploadStatusUpdated($this->upload));
        }
    }
}

