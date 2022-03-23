<?php

use App\Services\Bot\Handlers\Commands\FindCommand;
use App\Services\Bot\Handlers\Commands\HelpCommand;

return [
    "interface" => [
        "button" => [
            "find_by_list" => "Вибрати із списку",
            "find_by_location" => "Знайти поблизу",
            "my_location" => "Я зараз тут",
            "show_on_the_map" => "Відкрити на карті " . emoji("\xF0\x9F\x93\x8D"), // 📍
            "facebook" => "Facebook",
            "more" => "Більше " . emoji("\xE2\x8F\xA9"), // ⏩
            "wrong_address" => "Повідомити про помилку в адресі" . emoji("\xF0\x9F\x98\xA8"), /* 😨 */
            "cancel" => "Відмінити",
            "cannot_help" => "Не можу допомогти",
            "back" => "Назад",
            "confirm_yes" => "Так, все вірно",
            "confirm_no" => "Упс, є помилкочка",
            "help_project" => "Допомогти проекту",
        ],
    ],
    "messages" => [
        "text" => [
            // /start command message text
            "start" => "Привіт!" . PHP_EOL .
                "Я був створений для того, щоб допомогти тобі знайти церкву " . emoji("\xE2\x9B\xAA"), // ⛪

            // /help command message text
            "help" => "Ось мої основні можливості:" . PHP_EOL .
                // /find command description
                emoji('\xF0\x9F\x94\xB8') . /* 🔸 */
                " Для пошуку адреси викликай команду /" . FindCommand::COMMAND_SIGNATURE .
                " і просто дотримуйся інструкцій." . PHP_EOL .
                // /help command description
                emoji('\xF0\x9F\x94\xB8') . /* 🔸 */
                "Для отримання довідки використовуй команду /" . HelpCommand::COMMAND_SIGNATURE . PHP_EOL .
                // Support channel description
                emoji('\xF0\x9F\x94\xB8') .
                "У разі виникнення будь-яких труднощів звертайся на канал тенічної підтримки. " .
                "Також твої ідеї в покращенні є дуже цінними для мене, їх ти також можеш залишити на каналі технічної" .
                " підтримки: " .
                emoji("\xE2\x84\xB9") .  // ℹ️
                " [:support_channel_name](:support_channel_link)",

            // /find command message text
            "find" => "Ти можеш обрати церкву зі списку, або ж просто відправити " .
                "мені своє розташування і я знайду найближчу до тебе громаду.",

            "find_by_location" => "Відправ мені свою геолокацію",
            "find_by_list" => "В якій області ти шукаєш церкву?",
            "specify_a_city" => "Вкажи, будь ласка, населений пункт.",
            "specify_a_church" => "Яка саме церква тебе цікавить?",

            // Church address format
            "church_address" => "*:name* " . emoji("\xE2\x9B\xAA") /* ⛪ */ . PHP_EOL . ":address",

            // Incorrect request message
            "incorrect_request" => "Вибач, не можу опрацювати твій запит " . emoji("\xF0\x9F\x98\xA8") /* 😨 */ . PHP_EOL .
                "Можливо ти хотів би переглянути ще раз мої основні можливості, тоді використай команду /" .
                HelpCommand::COMMAND_SIGNATURE,

            "ask_for_the_address_correction" => "Вибач за незручності, я обов'язково виправлю це! " . PHP_EOL .
                "До речі ти можеш допомогти мені надіславши вірну адресу: " . PHP_EOL .
                "(поточна адреса: _:current_address_)",

            "confirm_address_request" => "Отже це актуальна адреса церкви *:church*? " . PHP_EOL .
                "_:address_",
            "thank_you_for_helping" => "Дякую за допомогу " . emoji("\xF0\x9F\x98\x89"), // 😉

            // quick messages
            "canceled" => "Дію відмінено " . emoji("\xF0\x9F\x9A\xAB"), // 🚫

            "support_info"  => "Усі пропозиції щодо покращень та уточнення приймаються " .
                "та обговорюються в каналі технічної підтримки: " .
                emoji("\xE2\x84\xB9") // ℹ️
                . " [:support_channel_name](:support_channel_link)",

            "inaccurate_data" => "Деякі адреси можуть бути не точними, або застарілими. " .
                "Буду радий отримати будь-яку допомогу щодо актуальзації адрес " . emoji("\xF0\x9F\x98\x89"), // 😉,

            "no_results_found" => "Нажаль мені не вдалось знайти жодної церкви в радіусі :radius км." .
                 emoji("\xF0\x9F\x98\x9E"), // 😞,
        ],
    ],
];
