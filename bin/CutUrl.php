<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dataBase=new \Illuminate\Database\Capsule\Manager();
$services = include('./../config/config.php');
$dataBase->addConnection([
    "driver"=>'mysql',
    "host"=>'db_mysql',
    "database"=>'base',
    "username"=>'doctor',
    "password"=>'pass4doctor',
]);
$dataBase->bootEloquent();

$prefix = "vily/";
$max_length_Url = 10;
echo '*******************' . PHP_EOL;
echo '<br>';
echo 'Hi. Start of program...' . PHP_EOL;
echo '<br>';
echo 'DB system among available list:'. PHP_EOL;
$last=array_key_last($services);
foreach ($services as $key=>$value)
{
    if($value===$services[$last]){
        echo $key.'.';
    }else{
        echo $key.',';}
}
echo PHP_EOL;
echo '<br>';
echo 'Your option: '.$_GET['db'];
echo '<br>';
$option='';
if($_GET['db']==='file'){
    $option='File';
}elseif ($_GET['db']==='mysql'){
    $option='mysql';
};
$container =new Container($services);
echo 'This what do you want to do with your URL' . PHP_EOL;
$action = '';
if(isset($_GET['code'])){
    $action='code';
    echo 'code';
}elseif ($_GET['encode']){
    $action='encode';
    echo 'encode';
};
echo '<br>';
try{
    $open_db=$container->get($option);
    $url_array=$open_db->getArrayOfUrl();
    switch ($action) {
        case 'code':
            $code = trim($_GET['code'], " \n");
            if (!trim($code, ' ')) {
                echo 'Empty field, for Long URL' . PHP_EOL;
                echo '<br>';
            } else {
                if (str_contains($code, "https://$prefix")) {
                    echo 'Incorrect long URL, probably this is short URL, check it please' . PHP_EOL;
                    echo '<br>';
                } else {
                    $check_in_DB_presence_url = new CheckUrl();
                    $position_url_in_array = $check_in_DB_presence_url->search($url_array, $code);
                    if ($position_url_in_array >= 0) {
                        echo 'Short URL exists for this site in our DB, here it is:' . PHP_EOL;
                        echo '<br>';
                        echo $url_array[$position_url_in_array - 1].PHP_EOL;
                        echo '<br>';
                    } elseif ($position_url_in_array === -1) {
                        echo 'Short URL not exists in our DB. We are checking what we could do......' . PHP_EOL;
                        echo '<br>';
                        try {
                            $new_short_url = new Enter();
                            $new_short_url->setter($prefix, $max_length_Url);
                            $value_new_short_url = $new_short_url->encode($code);
                            $full_short_url = "https://" . $prefix . $value_new_short_url . PHP_EOL;
                            $url_array[] = $full_short_url;
                            echo 'New generated short URL: ' . $full_short_url;
                            echo '<br>';
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            echo '<br>';
                            exit('Exit of program' . PHP_EOL);
                        }
                        $url_array[] = $code . PHP_EOL;
                        try {
                            $check_exist_in_web = new Exist($code);
                            if ($check_exist_in_web->exist($code)) {
                                echo "The site exists in Web. Server response: " . $check_exist_in_web->exist($code) . PHP_EOL;
                                echo '<br>';
                            };
                        } catch (Exception $e) {
                            echo $e->getMessage();
                            echo '<br>';
                        }

                        $write_file_with_url = $open_db->saveNewUrl($url_array);
                    }
                    break;
                }
            }
            break;
        case 'encode':
            $decode = trim($_GET['encode'], " \n");
            if (!trim($decode, ' ')) {
                echo 'Empty field, type Short URL' . PHP_EOL;
                echo '<br>';
            } else {
                if (str_contains($decode, "https://$prefix")) {
                    try {
                        $validate_short = new OutPut();
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        echo '<br>';
                        exit('Exit of program' . PHP_EOL);
                    }
                    $validate_short->setter($url_array);
                    echo 'Long URL: ' . $validate_short->decode($decode) . "which correspond to short ULR: $decode" . PHP_EOL;
                    echo '<br>';
                } else {
                    echo 'Incorrect short URL, maybe you typed Long URL' . PHP_EOL;
                    echo '<br>';
                }

            }
            break;
        default:
            echo 'We do not understand your request, make sure you provide correct info, choose CODE or DECODE' . PHP_EOL;
            echo '<br>';
    }
    echo 'Finish of program.' . PHP_EOL;
    echo '<br>';
    echo '*******************' . PHP_EOL;
    echo '<br>';
}catch (NotFoundServiceException $e) {
    echo '<br>';
    echo $e->getMessage();
};

