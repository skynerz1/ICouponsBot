<?php
// ========= إعداد ==========
$TOKEN = "8044751545:AAGlip1dHMQX61nlb8YZVogvyd28Oi1OmrQ";
$CHANNEL_USERNAME = "@JJF_l"; // القناة المطلوب الاشتراك فيها
$DEVELOPER_USERNAME = "@wgggk";
$website = "https://api.telegram.org/bot$TOKEN/";

// ========= جلب التحديث ==========
$update = json_decode(file_get_contents("php://input"), true);
$message = $update['message'] ?? null;
$chat_id = $message['chat']['id'] ?? null;
$text = $message['text'] ?? '';

// ========= ملفات المستخدمين ==========
function load_users() {
    $file = 'users.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function save_users($users) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$users = load_users();
$lang = $users[$chat_id]['lang'] ?? 'ar';
$page = $users[$chat_id]['page'] ?? 0;

// ========= اختيار اللغة ==========
if (in_array($text, ['🇸🇦 Arabic', '🇺🇸 English'])) {
    $lang = ($text == '🇸🇦 Arabic') ? 'ar' : 'en';
    $users[$chat_id]['lang'] = $lang;
    save_users($users);
}

// ========= الترجمة ==========
function t($key, $lang) {
    $ar = [
        'start_coupon' => '🚀 ابدأ الكوبونات',
        'change_lang' => '🌐 تغيير اللغة',
        'developer' => '👨‍💻 المطور',
        'choose_store' => 'اختر المتجر لعرض الكوبون:',
        'next' => '⏭ التالي',
        'back' => '⏮ الرجوع',
        'refresh' => '🔄 تحديث القائمة',
        'check_join' => '✅ تحقق من الاشتراك',
        'must_join' => '📛 يجب الاشتراك في القناة أولاً:',
        'lang_select' => '🌐 اختر اللغة:',
        'lang_ar' => '🇸🇦 العربية',
        'lang_en' => '🇺🇸 English',
        'lang_set_ar' => '✅ تم تعيين اللغة إلى العربية.',
        'lang_set_en' => '✅ Language set to English.',
        'back_to_menu' => '↩ الرجوع للقائمة الرئيسية',
        'subscribe_channel' => '📢 اشترك في القناة'
    ];
    $en = [
        'start_coupon' => '🚀 Start Coupons',
        'change_lang' => '🌐 Change Language',
        'developer' => '👨‍💻 Developer',
        'choose_store' => 'Choose a store to get a coupon:',
        'next' => '⏭ Next',
        'back' => '⏮ Back',
        'refresh' => '🔄 Refresh',
        'check_join' => '✅ Check Subscription',
        'must_join' => '📛 You must join the channel first:',
        'lang_select' => '🌐 Select language:',
        'lang_ar' => '🇸🇦 Arabic',
        'lang_en' => '🇺🇸 English',
        'lang_set_ar' => '✅ Language set to Arabic.',
        'lang_set_en' => '✅ Language set to English.',
        'back_to_menu' => '↩ Back to main menu',
        'subscribe_channel' => '📢 Subscribe to Channel'
    ];
    return ($lang == 'ar') ? ($ar[$key] ?? $key) : ($en[$key] ?? $key);
}

// ========= تحقق الاشتراك ==========
function isUserJoined($chat_id) {
    global $TOKEN, $CHANNEL_USERNAME;
    $url = "https://api.telegram.org/bot$TOKEN/getChatMember?chat_id=$CHANNEL_USERNAME&user_id=$chat_id";
    $response = json_decode(file_get_contents($url), true);
    $status = $response['result']['status'] ?? '';
    return in_array($status, ['member', 'administrator', 'creator']);
}

// ========= إرسال رسالة ==========
function sendMessage($chat_id, $text, $keyboard = null, $inline = null) {
    global $website;
    $replyMarkup = [];

    if ($keyboard) $replyMarkup['keyboard'] = $keyboard;
    if ($inline) $replyMarkup['inline_keyboard'] = $inline;
    if (!empty($replyMarkup)) $replyMarkup['resize_keyboard'] = true;

    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => !empty($replyMarkup) ? json_encode($replyMarkup) : null,
        'parse_mode' => 'Markdown'
    ];
    file_get_contents($website . "sendMessage?" . http_build_query($params));
}

// ========= المتاجر ==========
$stores = [
    ['ar' => 'كريم', 'en' => 'Careem', 'slug' => 'careem'],
    ['ar' => 'نون', 'en' => 'Noon', 'slug' => 'noon'],
    ['ar' => 'نون فود', 'en' => 'noon-food', 'slug' => 'noon-food'],
    ['ar' => 'تمو', 'en' => 'Temu', 'slug' => 'temu'],
    ['ar' => 'شي إن', 'en' => 'Shein', 'slug' => 'shein'],
    ['ar' => 'أمازون', 'en' => 'Amazon', 'slug' => 'amazon-saudi'],
    ['ar' => 'ترينديول', 'en' => 'trendyol', 'slug' => 'trendyol'],
    ['ar' => 'هنقرستيشن', 'en' => 'HungerStation', 'slug' => 'hungerstation'],
    ['ar' => 'ذا تشيلدرنز بليس', 'en' => "The Children's Place", 'slug' => 'the-childrens-place'],
    ['ar' => 'فوغا كلوسيت', 'en' => 'VogaCloset', 'slug' => 'vogacloset'],
    ['ar' => 'سوق', 'en' => 'Souq', 'slug' => 'souq'],
    ['ar' => 'زارا', 'en' => 'ZARA', 'slug' => 'zara'],
    ['ar' => 'نمشي', 'en' => 'Namshi', 'slug' => 'namshi'],
    ['ar' => 'أناس', 'en' => 'Ounass', 'slug' => 'ounass'],
    ['ar' => 'ايهيرب', 'en' => 'iherb', 'slug' => 'iherb'],
    ['ar' => 'صيدليه النهدي', 'en' => 'nahdi', 'slug' => 'nahdi'],

    // متاجر جديدة مضافة مع أكواد
    ['ar'=>'ددزل','en'=>'Diesel','slug'=>'diesel','code'=>'ALCPN'],
    ['ar'=>'لويزا فياروما','en'=>'Luisaviaroma','slug'=>'luisa-roma','code'=>'LOVE20'],
    ['ar'=>'كامبر','en'=>'Camper','slug'=>'camper'],
    ['ar'=>'نيو شيك','en'=>'Newchic','slug'=>'newchic'],
    ['ar'=>'800 فلور','en'=>'800 Flower','slug'=>'800-flower'],
    ['ar'=>'يووكس','en'=>'Yoox','slug'=>'yoox'],
    ['ar'=>'مانجو','en'=>'Mango','slug'=>'mango'],
    ['ar'=>'باشا سراي','en'=>'Basha Saray','slug'=>'bashasaray','code'=>'ALC'],
    ['ar'=>'عودي','en'=>'Oudy','slug'=>'oudy','code'=>'A47'], 
    ['ar'=>'عودي (10%)','en'=>'Oudy10','slug'=>'oudy','code'=>'A18'],
    ['ar'=>'ماركات','en'=>'Markat','slug'=>'markat','code'=>'ALCP10'],
    ['ar'=>'ماركات (7%)','en'=>'Markat7','slug'=>'markat','code'=>'MA100'],
    ['ar'=>'المبيعات الجوية','en'=>'Skysales','slug'=>'skysales','code'=>'ZA2'],
    ['ar' => 'اي اتش جي', 'en' => 'IHG', 'slug' => 'ihg'],
    ['ar' => 'كاسبر سكاي', 'en' => 'kaspersky', 'slug' => 'kaspersky'],
    ['ar' => 'ال جي', 'en' => 'lg', 'slug' => 'lg'],
    ['ar' => 'اسوس', 'en' => 'asos', 'slug' => 'asos'],
    ['ar' => 'فارفيتش', 'en' => 'farfetch', 'slug' => 'farfetch'],
    ['ar' => 'أناس', 'en' => 'Ounass', 'slug' => 'ounass'],
    ['ar' => 'ماكس فاشن', 'en' => 'max-fashion', 'slug' => 'max-fashion'],
    ['ar' => 'جييني', 'en' => 'jeeny', 'slug' => 'jeeny'],
    ['ar' => 'اديداس', 'en' => 'adidas', 'slug' => 'adidas'],
    ['ar' => 'كيتا', 'en' => 'keeta', 'slug' => 'keeta'],
    ['ar' => 'زد', 'en' => 'zid', 'slug' => 'zid'],

];

function get_coupon_code($slug) {
    $url = "https://saudi.alcoupon.com/ar/discount-codes/" . $slug;
    $html = @file_get_contents($url);
    if (preg_match('/<textarea[^>]*class="coupon-text[^>]*>([^<]+)<\/textarea>/', $html, $matches)) {
        return trim($matches[1]);
    }
    return "لا يوجد كود حالياً لهذا المتجر.";
}

// ========= بناء قائمة المتاجر ==========
function build_store_keyboard($lang, $page) {
    global $stores;
    $per_page = 10;
    $start = $page * $per_page;
    $sliced = array_slice($stores, $start, $per_page);
    $buttons = [];
    foreach ($sliced as $store) {
        $buttons[] = [['text' => $store[$lang]]];
    }
    $nav = [];
    if ($page > 0) $nav[] = ['text' => t('back', $lang)];
    if ($start + $per_page < count($stores)) $nav[] = ['text' => t('next', $lang)];
    if ($nav) $buttons[] = $nav;
    $buttons[] = [['text' => t('refresh', $lang)], ['text' => t('change_lang', $lang)]];
    $buttons[] = [['text' => t('back_to_menu', $lang)]];
    return $buttons;
}

// ========= الأوامر ==========
if ($text == "/start" || $text == t('back_to_menu', $lang)) {
    $buttons = [
        [["text" => t('start_coupon', $lang)]],
        [["text" => t('change_lang', $lang)]],
        [["text" => t('developer', $lang) . " $DEVELOPER_USERNAME"]],
    ];
    sendMessage($chat_id, "👋", $buttons);

} elseif ($text == t('start_coupon', $lang)) {
    if (!isUserJoined($chat_id)) {
        $buttons = [[["text" => t('check_join', $lang)]]];
        sendMessage($chat_id, t('must_join', $lang) . "\nhttps://t.me/" . str_replace("@", "", $CHANNEL_USERNAME), $buttons);
        exit;
    }
    $users[$chat_id]['page'] = 0;
    save_users($users);
    sendMessage($chat_id, t('choose_store', $lang), build_store_keyboard($lang, 0));

} elseif ($text == t('next', $lang)) {
    $users[$chat_id]['page'] = ++$page;
    save_users($users);
    sendMessage($chat_id, t('choose_store', $lang), build_store_keyboard($lang, $page));

} elseif ($text == t('back', $lang)) {
    $users[$chat_id]['page'] = $page = max(0, --$page);
    save_users($users);
    sendMessage($chat_id, t('choose_store', $lang), build_store_keyboard($lang, $page));

} elseif ($text == t('refresh', $lang)) {
    sendMessage($chat_id, t('choose_store', $lang), build_store_keyboard($lang, $page));

} elseif ($text == t('check_join', $lang)) {
    if (isUserJoined($chat_id)) {
        sendMessage($chat_id, t('choose_store', $lang), build_store_keyboard($lang, $page));
    } else {
        $buttons = [[["text" => t('check_join', $lang)]]];
        sendMessage($chat_id, t('must_join', $lang) . "\nhttps://t.me/" . str_replace("@", "", $CHANNEL_USERNAME), $buttons);
    }

} elseif ($text == t('change_lang', $lang)) {
    $buttons = [
        [["text" => t('lang_ar', $lang)], ["text" => t('lang_en', $lang)]],
        [["text" => t('back_to_menu', $lang)]]
    ];
    sendMessage($chat_id, t('lang_select', $lang), $buttons);

} elseif ($text == t('lang_ar', 'ar') || $text == t('lang_ar', 'en')) {
    $users[$chat_id]['lang'] = 'ar';
    save_users($users);
    sendMessage($chat_id, t('lang_set_ar', 'ar'));

} elseif ($text == t('lang_en', 'ar') || $text == t('lang_en', 'en')) {
    $users[$chat_id]['lang'] = 'en';
    save_users($users);
    sendMessage($chat_id, t('lang_set_en', 'en'));

} else {
    foreach ($stores as $store) {
        if ($text == $store[$lang]) {
            $code = get_coupon_code($store['slug']);
            $msg = "🎁 *كود خصم {$store[$lang]}:*\n\n`$code`\n\nBy $DEVELOPER_USERNAME";
            $inline = [
                [[
                    'text' => t('subscribe_channel', $lang),
                    'url' => "https://t.me/" . str_replace("@", "", $CHANNEL_USERNAME)
                ]]
            ];
            sendMessage($chat_id, $msg, null, $inline);
            exit;
        }
    }
    sendMessage($chat_id, "🤖 غير مفهوم: $text");
}
?>
