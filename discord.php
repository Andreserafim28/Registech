function enviarNotificacaoDiscord($mensagem) {
    $webhookurl = "https://discord.com/api/webhooks/1505919139066281994/zt06ZIQtfk6jcAKY0ELvONWSxyfcHntPPZzlIBlboS-6oQrnHwzgaz__zR7lIaOoORKj";

    $json_data = json_encode([
        "content" => $mensagem,
        "username" => "RegisTech Bot",
        "avatar_url" => "https://teu-site.com/logo.png" // Opcional
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);
}
