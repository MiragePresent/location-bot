<?php

use App\Services\Bot\Handlers\Commands\FindCommand;
use App\Services\Bot\Handlers\Commands\HelpCommand;

return [
    "interface" => [
        "button" => [
            "find_by_list" => "Вибрати із списку",
            "find_by_location" => "Знайти поблизу",
            "show_on_the_map" => "Відкрити на карті",
            "facebook" => "Facebook",
        ],
    ],
    "messages" => [
        "text" => [
            // /start command message text
            "start" => "Привіт!\n" .
                "Я був створений для того, щоб допомогти тобі знайти церкву " . emoji("\xE2\x9B\xAA"), // ⛪

            // /help command message text
            "help" => "Ось мої основні можливості:\n" .
                // /find command description
                emoji('\xF0\x9F\x94\xB8') . /* 🔸 */
                " Для пошуку адреси викликай команду /" . FindCommand::COMMAND_SIGNATURE .
                " і просто дотримуйся інструкцій.\n" .
                // Inline mode description
                emoji('\xF0\x9F\x94\xB8') . /* 🔸 */
                "Також можна скористатись пошуком прямо у чаті " .
                "(якщо попередньо додаси мене туди), звернувшись до мене: @:bot_username " .
                "та вказавши населений пункт. \n" .
                // /help command description
                emoji('\xF0\x9F\x94\xB8') . /* 🔸 */
                "Для отримання довідки використовуй команду /" . HelpCommand::COMMAND_SIGNATURE,

            // /find command message text
            "find" => "Ти можеш обрати церкву зі списку, або ж просто відправити " .
                "мені своє розташування і я знайду найближчу до тебе громаду.",

            // Church address format
            "church_address" => "*:name* " . emoji("\xE2\x9B\xAA") /* ⛪ */ . "\n:address",
        ],
    ],
];
