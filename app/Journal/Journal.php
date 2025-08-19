<?php

namespace App\Journal;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class Journal
{

    protected static ?string $filename = 'journal.log';

    protected static ?string $directory = 'journal/';

    public static function getJournalPath(): string
    {
        return self::$directory . self::$filename;
    }

    public static function createJournal(): void
    {
        if (!Storage::disk('public')->exists(self::$directory)) {
            Storage::disk('public')->makeDirectory(self::$directory);
        }
        if (!Storage::disk('public')->exists(self::$directory . self::$filename)) {
            Storage::disk('public')->put(self::$directory . self::$filename, '');
        }
    }

    public static function read(): string
    {
        $filePath = self::$directory . self::$filename;
        if (self::checkIfJournalExists()) {
            return Storage::disk('public')->get($filePath);
        }
        return 'No journal entries found.';
    }

    public static function append(string $message): void
    {
        $filePath = self::$directory . self::$filename;
        Storage::disk('public')->append($filePath, Date::now()->toDateTimeString() . ' - ' . $message);
    }

    public static function appendList(array $messages): void
    {
        foreach ($messages as $message) {
            self::append($message);
        }
    }

    public static function checkIfJournalExists(): bool
    {
        return Storage::disk('public')->exists(self::$directory . self::$filename);
    }

    public static function deleteJournal(): void
    {
        if (self::checkIfJournalExists()) {
            Storage::disk('public')->delete(self::$directory . self::$filename);
        }
    }
}
