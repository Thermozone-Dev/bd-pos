<?php

namespace App\Journal;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class Journal
{

    protected static ?string $filename = 'journal.txt';

    protected static ?string $directory = 'journal';

    public static function getJournalPath(): string
    {
        return self::$directory . '/' . self::$filename;
    }

    public static function journalDirectoryExists(): bool
    {
        return Storage::disk('public')->exists(self::$directory);
    }

    public static function createJournalDirectory(): void
    {
        if (!Storage::disk('public')->exists(self::$directory)) {
            Storage::disk('public')->makeDirectory(self::$directory);
        }
    }

    public static function getJournalEntries(): Collection
    {
        $files = Storage::disk('public')->files(self::$directory, true);
        $data = collect();
        foreach ($files as $file) {
            if (str_ends_with($file, '.log')) {
                $data->push([
                    'name' => explode('/', $file)[1],
                    'path' => $file,
                    'size' => Storage::disk('public')->size($file),
                    'created_at' => Date::createFromTimestamp(Storage::disk('public')->lastModified($file))->toDateTimeString(),
                ]);
            }
        }

        return $data;
    }

    public static function createDateDirectory(string $date): void
    {
        if (!Storage::disk('public')->exists(self::$directory . "/" . self::$directory . '-' . $date)) {
            Storage::disk('public')->makeDirectory(self::$directory . "/" . self::$directory . '-' . $date);
        }
    }

    public static function createJournalEntry(string $date, string $type, array $content) {
        self::createDateDirectory($date);

        $filePath = self::$directory . "/" . self::$directory . '-' . $date . '/' . $type . "-000000001" . '.log';

        while (Storage::disk('public')->exists($filePath)) {
            $numberString = explode('-', explode('.', $filePath)[0]);
            $number = (int)end($numberString);
            $number++;
            $filePath = self::$directory . "/" . self::$directory . '-' . $date . '/' . $type . "-" . str_pad($number, 9, '0', STR_PAD_LEFT) . '.log';
        }

        Storage::disk('public')->put($filePath, '');

        self::appendList($filePath, $content, true);
    }

    public static function read(string $filePath): string
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->get($filePath);
        }
        return 'No journal entries found.';
    }

    public static function append(string $filePath, string $message, bool $is_raw = false): void
    {
        if ($is_raw){
            Storage::disk('public')->append($filePath, $message);
            return;
        }
        else {
            Storage::disk('public')->append($filePath, Date::now()->toDateTimeString() . ' - ' . $message);
            return;
        }
    }

    public static function appendList(string $filePath, array $messages, bool $is_raw = false): void
    {
        foreach ($messages as $message) {
            self::append($filePath, $message, $is_raw);
        }
    }

    public static function clearJournalMergeFile(): void
    {
        if (Storage::disk('public')->exists(self::getJournalPath())) {
            Storage::disk('public')->delete(self::getJournalPath());
            Storage::disk('public')->put(self::getJournalPath(), '');
        }
    }
}
