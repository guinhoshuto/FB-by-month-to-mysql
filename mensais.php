<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/config.php';

    try {
        $response = $fb->get(
            'act_2212482835703434/insights?fields=reach,social_spend,spend,impressions,unique_clicks&date_preset=last_month',
            $token
        );
    } catch (FacebookExceptionsFacebookResponseException $e){
        echo 'Graph returned an error: ' . $e->getMessage();  
        exit; 
        } catch (FacebookExceptionsFacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();  
            exit;
    }
    $fbObj = $response->getGraphEdge();

    $mes = json_decode($fbObj);
    $_mes = explode("-",$mes[0]->date_start);
    $mesFormatado = $_mes[0] . "-" .  $_mes[1] . "-" . "01";
    $select = "SELECT mes FROM `facebook-ads-mensal` WHERE mes='" . $mesFormatado . "'";  
    echo $select;
    echo '<pre>';
    print_r($mes);

    $_cadastrado = mysqli_query($conn,$select);
    if(mysqli_num_rows($_cadastrado) > 0){
        $update = "UPDATE `facebook-ads-mensal` SET 
                   alcance='" . $mes[0]->reach . 
                   "', social_spend='" . $mes[0]->social_spend . 
                   "', spend='" . $mes[0]->spend . 
                   "', impressions='" .$mes[0]->impressions .
                   "', clicks='" .$mes[0]->unique_clicks .
                   "' WHERE mes='" . $mesFormatado . "'";
        if(mysqli_query($conn, $update)){
            $msg = "atualizado com sucesso";
        } else {
            $msg = "erro ao atualizar";
        }
        echo $msg;
    } else {
        $insert = "INSERT into `facebook-ads-mensal` values('" . 
                   $mesFormatado . "', '" .
                   $mes[0]->reach . "', '" . 
                   $mes[0]->social_spend . "', '" . 
                   $mes[0]->spend . "', '" . 
                   $mes[0]->impressions . "', '" . 
                   $mes[0]->unique_clicks . "')";  
        
        echo $insert;
        if(mysqli_query($conn, $insert)){
            $msgi = 'gravado com sucesso';
        } else {
            $msgi = 'erro ao gravar';
        }
        echo $msgi;
    }
    mysqli_close($conn);
?>