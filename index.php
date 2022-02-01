<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Iniciar - iChat | APIGratis</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" href="https://imaladireta.com/images/icon/whatsapp.png" type="image/png">
    <link rel="apple-touch-icon" sizes="194x194" href="https://imaladireta.com/images/icon/whatsapp.png" type="image/png">
    <link rel="stylesheet" id="style" href="https://apigratis.com.br/static/css/demo/qrcode.css">
</head>

<body>
    <div id="app">

        <div class="app-wrapper app-wrapper-web">
            <div id="wrapper">
                <div id="window">

                    <div class="entry-main">
                        <div class="qrcode">
                            <img alt="Leia o QRCODE" id="base64" style="display: block;" src="https://imaladireta.com/images/icon/whatsapp.png">
                        </div>

                        <div class="entry-text" style="text-align: center;">
                            <div class="entry-title">Chat Demo APIGratis</div>
                            <div class="entry-subtitle">Use o WhatsApp no seu celular para ler o QRCODE</div>

                            <div class="entry-controls">
                                <label> <button type="button" id="buttonStart" class="btn btn-primary"> Conectar nova sessão</button> </label>
                                <div class="hint">Será iniciado uma sessão com nome inserido no javascript</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.socket.io/4.3.2/socket.io.min.js" integrity="sha384-KAZ4DtjNhLChOB/hxXuKqhMLYvx3b5MlT55xPEiNmREKRzeEm+RVPlTnAn0ajQNs" crossorigin="anonymous"></script>

<script>

    $(document).ready(() => {

        const SERVIDOR      =   `https://whatsapp-srv07.apigratis.com.br`
        const apitoken      =   `5E22BC1231XGFQ36B7CEA234F35C47651A6`
        const session       =   `SUA_SESSION`
        const sessionkey    =   `SUA_SESSIONKEY`

        const wh_connect    =   ``   //webhook
        const wh_message    =   ``   //webhook
        const wh_status     =   ``    //webhook
        const wh_qrcode     =   ``    //webhook

        try {

            socket = io(`${SERVIDOR}`, {
                withCredentials: false,
            });

        } catch (error) {
            console.log('API Desconectada!!! cd /opt/MyZap2.x.x.x node start index.js')
        }

        $(`#buttonStart`).on(`click`, async () => {
            await requestMyZap(apitoken, session, sessionkey, wh_connect, wh_message, wh_status, wh_qrcode, 'start')
        })

        async function requestMyZap(apitoken, sessionkey, session, wh_connect, wh_message, wh_status, wh_qrcode, action) {

            let URL = `${SERVIDOR}/${action}`

            socket.on(`events`, (events) => {

                $(".hint").html(`<span style="color: green;">${events?.message}</span>`)

                if (events.session == session) {

                    $(`#base64`).attr(`src`, events?.qrCode);
                    $(`#base64`).LoadingOverlay("hide")
                    $(`#buttonStart`).attr(`disabled`, true)
                    $(`#base64`).LoadingOverlay("hide")

                    if (events?.state === 'CONNECTED' && events?.status === 'inChat') {

                        $("#base64").attr("src", 'https://smartpink.com.br/site/assets/img/check.gif');
                        setTimeout(() => {
                            window.location.replace(`/chat?sessionkey=${sessionkey}&session=${session}`);
                        }, 4000)

                    }

                }

            })

            switch (action) {

                case `start`:
                    await $.post({
                        url: `${URL}`,
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'apitoken': `${apitoken ?? ''}`,
                            'sessionkey': `${sessionkey ?? ''}`
                        },
                        data: JSON.stringify({
                            session: session ?? '',
                            wh_connect: wh_connect ?? '',
                            wh_message: wh_message ?? '',
                            wh_status: wh_status ?? '',
                            wh_qrcode: wh_qrcode ?? '',
                        }),
                        beforeSend: function(data, xhr) {
                            $(`#base64`).LoadingOverlay("show")
                        },
                        success: function(data) {

                            console.log(data)
                            $(`#buttonStart`).attr(`disabled`, false)

                        },
                        error: function(error) {
                            console.log(error)
                            $(`#base64`).LoadingOverlay("hide")
                        },
                    })
                    break;

                default:
                    console.log('requisição inválida.')
                    break;
            }

        }

    });

</script>