<?php

spl_autoload_register('autoload');

function autoload($name)
{
    include_once __DIR__ . '/' . $name . '.php';
}

set_time_limit(0);

$config = include_once __DIR__ . "/SRconfig.php";

header("Content-Type: text/html; charset=utf-8");

if (isset($_POST['name'])) {
    $srApi = new SRApi($config['company'], $config['token']);

    $utmSource = $_POST['utm_source'] ?? '';
    $utmCampaign = $_POST['utm_campaign'] ?? '';
    $utmMedium = $_POST['utm_medium'] ?? '';
    $utmTerm = $_POST['utm_term'] ?? '';
    $utmContent = $_POST['utm_content'] ?? '';
 
    $query = "mutation addOrder(\$input: AddOrderInput!) {orderMutation {addOrder(input: \$input) {id}}}";

    $vars = [
        'input' => [
            'statusId' => $config['statusId'],
            'projectId' => $config['projectId'],
            'orderData' => [
                'humanNameFields' => [
                    [
                        'field' => $config['nameField'],
                        'value' => [
                            'firstName' => $_POST['name'],
                            'lastName' => ''
                        ]
                    ]
                ],
                'phoneFields' => [
                    [
                    'field' => $config['phoneField'],
                    'value' => $_POST['phone']
                    ]
                ]
                    ],
                    'source' => [
                        'refererUri' => $_SERVER['HTTP_REFERER'],
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'utm_source' => $utmSource,
                        'utm_campaign' => $utmCampaign,
                        'utm_medium' => $utmMedium,
                        'utm_term' => $utmTerm,
                        'utm_content' => $utmContent
                    ]
        ]
    ];

    
    $cartItems = [
        
        'items' => [
            [
                'itemId' => (int) $config['itemId'],
                'quantity' => (int) ($_POST['quantity'] ?? 1),
                'variation' => 1,
                'price' => (int) round(((float) $config['priceItem']) * 100)
            ]
        ]
            
    ];
    
    
    if (!empty($cartItems)) {
        $vars['input']['cart'] = $cartItems;
    }

    $result = $srApi->sendRequest($query, $vars);

    FileLogger::recordsLogs("responseLog.txt", $srApi->jsonEncode($vars));
    FileLogger::recordsLogs("responseLog.txt", $result);
}

/* https://api.telegram.org/bot7805364611:AAHVnWyh6QgLXzEKxx6EfubSx2jwOJOPpFk/getUpdates,
где, XXXXXXXXXXXXXXXXXXXXXXX - токен вашего бота, полученный ранее */
$title = "вашдомен/"; #Заголовок письма
$name = $_POST['name'];                             
$phone = $_POST['phone'];
$token = "токенбот";
$chat_id = "айдичата";
$arr = array(
  'Тяпка для прополки _29.99 BYN_Ф.И.О: ' => $name,
  'Номер Телефона: ' => $phone,
   'ссылка ' => $title
);
$txt = '';
foreach($arr as $key => $value) {
  $txt .= "<b>".$key."</b>".$value."%0A";
};

$sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");

if ($sendToTelegram) {
   header('Location: /tyapka-dlya-propolki/private/thanksPage/good.html');
}else{ 
  echo "no";
}


#Начало скрипта для отправки на почту
 function createOrder($name, $phone) {
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://online.moysklad.ru/api/remap/1.2/entity/customerorder",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"description\":\"$name $phone\",\n   \"positions\":[\n      {\n         \"quantity\":1,\n         \"price\":3999,\n         \"discount\":0,\n         \"assortment\":{\n            \"meta\":{\n               \"href\":\"https://online.moysklad.ru/api/remap/1.2/entity/product/5902f044-21d7-11ec-0a80-0845000b523c/images\",\n               \"metadataHref\":\"https://online.moysklad.ru/api/remap/1.2/entity/product/metadata\",\n               \"type\":\"product\",\n               \"mediaType\":\"application/json\"\n            }\n         },\n         \"reserve\":0\n      }\n   ],\n\t\"store\":{\n      \"meta\":{\n          \"href\": \"https://online.moysklad.ru/api/remap/1.2/entity/store/88cf3503-1ec8-11eb-0a80-05ca000d31a5\",\n    \"metadataHref\": \"https://online.moysklad.ru/api/remap/1.2/entity/store/metadata\",\n    \"type\": \"store\",\n\t\t\"mediaType\": \"application/json\"\n      }\n   },\n\t\n   \"organization\":{\n      \"meta\":{\n         \"href\":\"https://online.moysklad.ru/api/remap/1.2/entity/organization/88ce4293-1ec8-11eb-0a80-05ca000d31a3\",\n         \"type\":\"organization\",\n         \"mediaType\":\"application/json\"\n      }\n   },\n\t\n   \"agent\":{\n      \"meta\":{\n         \"href\":\"https://online.moysklad.ru/api/remap/1.2/entity/counterparty/cdecd2e7-57a3-11ee-0a80-0b7d000d7e87\",\n         \"type\":\"counterparty\",\n         \"mediaType\":\"application/json\"\n      }\n   },\n\t\"attributes\": [\n    {\n      \"meta\": {\n        \"href\": \"https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/b09fa76e-1f98-11eb-0a80-023700012f3c\",\n        \"type\": \"attributemetadata\",\n        \"mediaType\": \"application/json\"\n      },\n      \"id\": \"b09fa76e-1f98-11eb-0a80-023700012f3\",\n      \"name\": \"Товар\",\n      \"type\": \"customentity\",\n      \"value\": {\n        \"meta\": {\n            \"href\": \"https://online.moysklad.ru/api/remap/1.2/entity/customentity/9bbcb15f-1f98-11eb-0a80-023700012e13/59b49b19-57a4-11ee-0a80-0405000e6162\",\n          \"metadataHref\": \"https://online.moysklad.ru/api/remap/1.2/context/companysettings/metadata/customEntities/9bbcb15f-1f98-11eb-0a80-023700012e13\",\n          \"type\": \"customentity\",\n          \"mediaType\": \"application/json\",\n          \"uuidHref\": \"https://online.moysklad.ru/app/#custom_9bbcb15f-1f98-11eb-0a80-023700012e13/edit?id=40000600-35fc-11eb-0a80-03c50003b669\"\n        },\n        \"name\": \"Диспенсер для пищевой пленки\"\n      }\n    }\n\t]\n\t\n}",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer 364493870ac2f233db9220edff517bbb2750dde5",
              "Content-Type: application/json"
            ],
        ]);
      
      
      
        $err = 1;
        while ($err) {
            $response = curl_exec($curl);
            $err = curl_error($curl);
            if (json_decode($response) && !$err) {
                break;
            }
        }
        curl_close($curl);
        file_put_contents('log.txt', PHP_EOL . date('l jS \of F Y h:i:s A'), FILE_APPEND);
        file_put_contents('log.txt', $response, FILE_APPEND);
        if (!empty($err)) {
            file_put_contents('log.txt', PHP_EOL . date('l jS \of F Y h:i:s A'), FILE_APPEND);
            file_put_contents('log.txt', 'Error:' . $err, FILE_APPEND);
        }
    //if ($name == '1'){ echo $response; die();}
        
    }


?>




