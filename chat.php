<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Chat - iChat | APIGratis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://apigratis.com.br/static/css/demo/chat.css" rel="stylesheet" type="text/css">
</head>

<body>
    
    <div class="container">

        <div class="panel messages-panel" style="margin-top:40px !important;">
            <div class="contacts-list">

                <div class="inbox-categories">
                    <div data-toggle="tab" data-target="#inbox" class="active" style="width:100%"> Todas as mensagens </div>
                </div>

                <div class="tab-content">

                    <div id="inbox" class="contacts-outter-wrapper tab-pane active">
                        <div class="contacts-outter">
                            <ul class="list-unstyled contacts">

                                <?php

                                    $sessionkey = $_GET["sessionkey"];
                                    $session = $_GET["session"];

                                    $chats = json_decode(ApiGratis\ApiBrasil::WhatsAppService("getAllChats", [
                                        "serverhost" => "https://whatsapp2.contrateumdev.com.br", //required
                                        "session" => $session, //required
                                        "sessionkey" => $sessionkey, //required
                                    ]));

                                    foreach ($chats->contacts as $key=>$chat)
                                    {
                                        if($chat->isGroup != true){

                                            $foto = isset($chat->contact->profilePicThumbObj->eurl) ? $chat->contact->profilePicThumbObj->eurl : "https://www.ecp.org.br/wp-content/uploads/2017/12/default-avatar-1-300x300.png";
                                            $nome = isset($chat->contact->pushname) ? $chat->contact->pushname : "Indisponível";
                                            $fone = isset($chat->contact->id->user) ? $chat->contact->id->user : "Indisponível";
    
                                            $first = $key == 0 ? "active" : "";

                                            echo '<li data-toggle="tab" data-target="#inbox-message-1" class="'.$first.'" onclick="getChatNumber('.$fone.')">
                                                    <img alt="" class="img-circle medium-image" src="'.$foto.'" style="margin-top:-65px;">
                
                                                    <div class="vcentered info-combo">
                                                        <h3 class="no-margin-bottom name"> '.$nome.' </h3>
                                                        <h5> <i class="fa fa-whatsapp"></i> '.$fone.' </h5>
                                                    </div>
                
                                                    <div class="contacts-add">
                                                        <span class="message-time"> 2:32</span>
                                                    </div>
                                                </li>';

                                        }
                                        
                                    }
                                ?>
                                
                            </ul>
                        </div>
                    </div>

                </div>

            </div>

            <div class="tab-content">

                <div class="tab-pane message-body active" id="inbox-message-1">

                    <div class="message-chat">

                        <div class="chat-body">

                          <!--   <div class="message info">
                                <img alt="" class="img-circle medium-image" src="https://www.ecp.org.br/wp-content/uploads/2017/12/default-avatar-1-300x300.png">

                                <div class="message-body">
                                    <div class="message-info">
                                        <h4> Elon Musk </h4>
                                        <h5> <i class="fa fa-clock-o"></i> 2:22 PM </h5>
                                    </div>
                                    <hr>
                                    <div class="message-text">
                                        I've seen your new template, Dauphin, it's amazing !
                                    </div>
                                </div>
                                <br>
                            </div>

                            <div class="message my-message">
                                <img alt="" class="img-circle medium-image" src="https://www.ecp.org.br/wp-content/uploads/2017/12/default-avatar-1-300x300.png">

                                <div class="message-body">
                                    <div class="message-body-inner">
                                        <div class="message-info">
                                            <h4> Dennis Novac </h4>
                                            <h5> <i class="fa fa-clock-o"></i> 2:28 PM </h5>
                                        </div>
                                        <hr>
                                        <div class="message-text">
                                            Thanks, I think I will use this for my next dashboard system.
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
 -->
                        </div>

                        <div class="chat-footer">
                            <textarea class="message" id="message"></textarea>
                            <button type="button" class="send-message-button btn-info btn-send"> <i class="fa fa-send"></i> </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.socket.io/4.3.2/socket.io.min.js" integrity="sha384-KAZ4DtjNhLChOB/hxXuKqhMLYvx3b5MlT55xPEiNmREKRzeEm+RVPlTnAn0ajQNs" crossorigin="anonymous"></script>
        
    <script>
        $(document).ready(() => {

            //servidor MYZAP
            SERVIDOR = `https://whatsapp2.contrateumdev.com.br`;

            $(`.btn-send`).on('click', async (e) => {

                //dados da sessao
                sessionkey = `<?php echo $_GET['sessionkey'] ?>`
                session = `<?php echo $_GET['session'] ?>`

                //ações para o request dinamico
                action = `/sendText`
                text = $(`#message`).val() ?? 'Sem texto';
                data = {
                    'session': `${session}`,
                    'number': `${number_send}`,
                    'text': `${text}`
                }

                //faz request
                await requestMyZap(action, sessionkey, data);

            })

        })

        async function getChatNumber(number) {
            
            number_send = number;

            //inicia o socket
            const socket = io(SERVIDOR);

            //receivedMessage
            socket.on('received-message', (receivedMessage) => {
                console.log("received", receivedMessage);
                console.log("received-message", receivedMessage?.from);

                if (receivedMessage?.isGroupMsg == false && receivedMessage?.status == 'RECEIVED') {
                    $(".chat-body").append(`<div class="message info">
                        <img alt="" class="img-circle medium-image" src="https://www.ecp.org.br/wp-content/uploads/2017/12/default-avatar-1-300x300.png">
                        <div class="message-body">
                            <div class="message-info">
                                <h4> ${ receivedMessage?.name } </h4>
                                <h5> <i class="fa fa-clock-o"></i> ${receivedMessage?.datetime} </h5>
                            </div>
                            <hr>
                            <div class="message-text">
                            ${receivedMessage?.content}
                            </div>
                        </div>
                        <br>
                    </div>`);
                }
            })

            //sendMessage
            socket.on('send-message', (sendMessage) => {
                console.log("sendMessage", sendMessage);

                if (sendMessage?.status == 'SENT') {
                    $(".chat-body").append(`<div class="message my-message">
                        <img alt="" class="img-circle medium-image" src="https://www.ecp.org.br/wp-content/uploads/2017/12/default-avatar-1-300x300.png">
                        <div class="message-body">
                            <div class="message-body-inner">
                                <div class="message-info">
                                    <h4> ${sendMessage?.name} </h4>
                                    <h5> <i class="fa fa-clock-o"></i> ${sendMessage?.datetime} </h5>
                                </div>
                                <hr>
                                <div class="message-text">
                                    ${sendMessage?.content}
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>`);

                }
                
            })

            //ack
            /* socket.on('ack', (ack) => {
                console.log('ack', ack);
            }) */

        }

        //request dinamica, você nao precisa reescrever isso novamente.
        async function requestMyZap(action, sessionkey, data) {
            $(`.message`).val('');
            try {
                let response = await fetch(`${SERVIDOR}${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'sessionkey': sessionkey
                    },
                    body: JSON.stringify(data)
                });
            } catch (error) {
                alert(`Erro ao enviar mensagem, verifique a conexão com a API.`);
                return false
            }
        }

    </script>

</body>

</html>