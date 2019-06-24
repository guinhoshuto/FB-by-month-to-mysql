<?php
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
require __DIR__ . '/config.php';

try {
    // Returns a `FacebookFacebookResponse` object
$response = $fb->get(
    '/' . $instagram_id . '?fields=name,insights.metric(audience_city,audience_gender_age).period(lifetime),followers_count,follows_count,media{media_type,media_url,permalink,like_count,timestamp,insights.metric(impressions,reach,engagement)}',
    $token
);
} catch(FacebookExceptionsFacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
    } catch(FacebookExceptionsFacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
$teste = $response->getGraphNode();

// $graphNode = $response->getGraphNode();
$posts = json_decode($teste)->media;
echo '<pre>';
print_r($posts);


foreach($posts as $post){
    foreach($post->insights as $insights){
        switch($insights->name){
            case 'impressions':
                $post->impressions = $insights->values[0]->value;
                break;
            case 'reach':
                $post->reach = $insights->values[0]->value;
                break;
            case 'engagement':
                $post->engagement = $insights->values[0]->value;
                break;
        }
    }


    $select = "SELECT id FROM `ig-posts-insights` WHERE id ='" . $post->id . "'";
    echo $select;
    $publicacao = mysqli_query($conn, $select);
    echo '<pre>';
    print_r($post);
    if(mysqli_num_rows($publicacao) > 0 ){
        echo 'post registrado <br>'; 
        $update = "UPDATE `ig-posts-insights` SET 
                    type='" . $post->media_type . "'," .
                    "media_url='" . $post->media_url . "'," .
                    "permalink='" . $post->permalink . "'," .
                    "engagement='" . $post->engagement . "'," . 
                    "reach='" . $post->reach . "'," . 
                    "impressions='" . $post->impressions . "'," .
                    "timestamp='" . $post->timestamp . "'," . 
                    "updated='" . date("Y-m-d H:i:s") . 
                    "' WHERE id = '" . $post->id . "'";

        echo $update;
        if(mysqli_query($conn,$update)){
            $msg = "Atualizado com sucesso!";
        }else{
            $msg = "Erro ao atualizar!";
        }
        echo $msg;

    } else {
        $insert = "insert into `ig-posts-insights` values('" 
                . $post->id . "','"  
                . $post->media_type . "','"
                . $post->media_url . "','"
                . $post->permalink . "','"
                . $post->engagement . "','"
                . $post->reach . "','"
                . $post->impressions . "','"
                . $post->timestamp . "','"
                . date("Y-m-d H:i:s") . "')";
                    
        echo $insert;
        if(mysqli_query($conn,$insert)){
            $msg = "Gravado com sucesso!";
        }else{
            $msg = "Erro ao gravar!";
        }
        echo $msg;
    }
}

echo '<pre>';
print_r($posts);

mysqli_close($conn);
