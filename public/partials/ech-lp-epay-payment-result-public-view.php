<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Landing_Epay
 * @subpackage Ech_Landing_Epay/public/partials
 */
?>

<?php 

    $transID = get_query_var('transid', 'none');
    if ($transID == 'none') { echo "<script>window.location.replace('/')</script>"; }


    $plugin_info = new Ech_Landing_Epay();
    $plugin_public = new Ech_Landing_Epay_Public($plugin_info->get_plugin_name(), $plugin_info->get_version());
 
    $getInfo = $plugin_public->getPaymentInfoByTransID($transID);
    $infoData = json_decode($getInfo, true);

    /* echo '<pre>';
    print_r($infoData);
    echo '</pre>'; */
?>


<?php if ( isset($infoData['status']) && $infoData['status'] == "PAID" ): ?>
    <?php 
        $paymentDetails = $infoData['paymentDetails'][0];
        if ( $paymentDetails['status'] == "COMPLETED" ) {

            $amount = $infoData['amount'] / 100; // 變回非兩位小數值
            // send email to customer
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $toEmail = $infoData['additionalInfo']['email'];
            $subject = "DR REBORN預付";
            $message = '<p>多謝預付</p>
                        <p>交易編號: ' . $infoData['clientTransactionId']. ' </p>
                        <p>預約資料: ' . $infoData['description'] . ' </p>
                        <p>預付金額: ' . $infoData['currency'] . ' ' . $amount . ' </p>
                        '; 
            
            $isEmailSent = wp_mail( $toEmail, $subject, $message, $headers);
            if ( $isEmailSent ) {
                echo '
                    <div><h2>多謝! 完成付款, 確認電郵已發送</h2></div>
                ';
            } else {
                echo '
                    <div><h2>多謝! 完成付款, 但發送確認電郵失敗, 請截屏以下資料再顯示給門市職員</h2></div>
                    <div>交易編號: ' . $infoData['clientTransactionId']. '</div>
                    <div>預約資料: ' . $infoData['description'] . '</div>
                    <div>預付金額: ' . $infoData['currency'] . ' ' . $amount . '</div>
                ';
            }// if ( $isEmailSent )
        } // $paymentDetails['status'] == "COMPLETED" 

    ?>

<?php else: ?>
    <script>
        alert('支付出錯，請重新支付');
        history.back();
    </script>";


<?php endif; // infoData['state'] == PAID ?>


