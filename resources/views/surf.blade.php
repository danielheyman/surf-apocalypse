<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SurfApacolypse</title>

        <link href="{{ elixir('css/global.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/surf.css') }}" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
    		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    	<![endif]-->
    </head>

    <body>

        <div class="loading">Loading Life...</div>
        <div class="wrapper">
            <div class="top">
                <div class="billboard-chain-left"></div>
                <div class="billboard-chain-right"></div>
                <div class="billboard"></div>
                <div class="billboard-shadow"></div>
                <div class="billboard-sign"></div>
                <div class="frame-wrapper">
                    <iframe src="http://clicktrackprofit.com"></iframe>
                </div>
            </div>

            <div class="bottom">
                <div class="character"></div>
                <div class="footer-fence"></div>
                <div class="footer">
                    <div class="inner">
                        <div class="left" id="chat">
                            <div class="messages">
                                <div class="message" v-repeat="message: messages">
                                    <span>@{{ message.name }}:</span>@{{ message.text }}
                                </div>
                            </div>
                            <div class="chat-menu">
                                <div class="form">
                                    <div class="type">Global</div>
                                    <input type="text" class="message" v-on="keyup: sendMessage | key 'enter'" v-model="message"/>
                                </div>
                                <div class="send" v-on="click: sendMessage">Send</div>
                            </div>
                        </div>
                        <div class="right">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="{{ elixir('js/global.js') }}"></script>
        <script src="{{ elixir('js/surf.js') }}"></script>
    </body>

</html>
