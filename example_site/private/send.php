<?php
/**
 * send.php — приём заказа с лендинга
 * Значения по умолчанию можно править прямо тут:
 */
spl_autoload_register(function($name){
    include_once __DIR__ . '/' . $name . '.php';
});

set_time_limit(0);
header("Content-Type: text/html; charset=utf-8");

$config = include_once __DIR__ . "/SRconfig.php";

/** ====== РЕДАКТИРУЕМЫЕ КОНСТАНТЫ (бот подставляет их при сборке) ====== */
const TARGET_EMAIL            = 'info@example.com';              // <- e-mail получателя
const PRODUCT_TITLE_DEFAULT   = 'Заявка с лендинга';             // <- заголовок письма (название товара)
const PAGE_URL_DEFAULT        = 'http://example.com/landing';    // <- полная ссылка на страницу
/** ===================================================================== */

function val($arr, $key, $def=''){ return isset($arr[$key]) ? trim((string)$arr[$key]) : $def; }
function valid_email($e){ return (bool)filter_var($e, FILTER_VALIDATE_EMAIL); }

if (!isset($_POST['name'], $_POST['phone'])) {
    http_response_code(400);
    echo "Ошибка: нет данных формы.";
    exit;
}

/** входные значения пользователя */
$userName     = val($_POST, 'name');
$userPhone    = val($_POST, 'phone');

/**
 * По умолчанию берём константы выше.
 * Если когда-то решишь передавать через форму, эти поля могут их переопределить:
 *  - target_email, product_title, page_url
 */
$targetEmail  = valid_email(val($_POST, 'target_email')) ? val($_POST,'target_email') : TARGET_EMAIL;
$productTitle = val($_POST,'product_title', PRODUCT_TITLE_DEFAULT) ?: PRODUCT_TITLE_DEFAULT;
$pageUrl      = val($_POST,'page_url', PAGE_URL_DEFAULT) ?: PAGE_URL_DEFAULT;

$title = $productTitle;
$text  =
"Заявка с сайта: $pageUrl

Информация о покупателе:
Имя: $userName
Телефон: $userPhone

Название товара: $productTitle

Время заказа: " . date("Y-m-d H:i:s");

/** — ваша бизнес-логика CRM — */
createOrder($userName, $userPhone);

/** — SR API — */
$srApi = new SRApi($config['company'], $config['token'], SRApi::API_CPA_SCOPE);
FileLogger::recordsLogs("post.txt", $srApi->jsonEncode($_POST));

$utmSource   = val($_POST,'utm_source');
$utmCampaign = val($_POST,'utm_campaign');
$utmMedium   = val($_POST,'utm_medium');
$utmTerm     = val($_POST,'utm_term');
$utmContent  = val($_POST,'utm_content');

$query = "mutation (\$input: AddLeadInput!) {leadMutation {addLead (input: \$input) {id}}}";
$vars = [
  'input' => [
    'offerId' => $config['offerId'],
    'cart' => ['items' => [[
      'itemId'   => (int)($config['itemId'] ?? 0),
      'quantity' => (int)($_POST['quantity'] ?? 1),
      'variation'=> 1
    ]]],
    'data' => [
      'phone_1' => $userPhone,
      'humanName_1' => ['firstName' => $userName, 'lastName' => '']
    ],
    'source' => [
      'refererUri'  => $_SERVER['HTTP_REFERER'] ?? $pageUrl,
      'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
      'utm_source'  => $utmSource,
      'utm_campaign'=> $utmCampaign,
      'utm_medium'  => $utmMedium,
      'utm_term'    => $utmTerm,
      'utm_content' => $utmContent
    ]
  ]
];

$price = (int) round(((float) ($config['priceItem'] ?? 0)) * 100);
if ($price > 0) { $vars['input']['cart']['items'][0]['price'] = $price; }

$result = $srApi->sendRequest($query, $vars);
FileLogger::recordsLogs("responseLog.txt", $srApi->jsonEncode($vars));
FileLogger::recordsLogs("responseLog.txt", $result);

/** — отправка письма — */
$headers  = "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

if (mail($targetEmail, $title, $text, $headers)) {
  header('Location: thanksPage/good.html');
} else {
  echo "Ошибка отправки письма. Возможно, функция mail() отключена на хостинге.";
}
